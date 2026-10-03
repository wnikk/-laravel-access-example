<?php

namespace App\Http\Controllers\Examples;

use App\Enum\Ability;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Wnikk\LaravelAccessRules\Conditions\Cond;
use Wnikk\LaravelAccessRules\Facades\Access;

/**
 * Example 9: the scenarios of docs/tutorial-abac-step-by-step.md on the six orders.
 *
 * Every scenario takes the permissions for orders away from Ann, Bob and the role "manager",
 * grants what its example of the article grants, and shows which orders open. "listed" comes
 * from the filter of a list, "opened" from a check of every record; the two always agree.
 * "expected" is the result the article states, where it states one.
 *
 *     /example9                 what can be tried
 *     /example9/cost-and-count  apply one scenario and see the result
 *     /example9/reset           back to the state of the seeders
 */
class Example9Controller extends Controller
{
    public function index()
    {
        return Response::json(array_map(
            fn (array $scenario, string $name): array => ['try' => url('/example9/'.$name), 'article' => $scenario['article'], 'what' => $scenario['what']],
            $this->scenarios(),
            array_keys($this->scenarios()),
        ));
    }

    public function show(string $name)
    {
        $scenario = $this->scenarios()[$name] ?? abort(404, 'Unknown scenario, see /example9');
        $ability  = $scenario['ability'] ?? 'orders.view';

        $owners = [
            'ann'     => Access::for(User::findOrFail(1)),
            'bob'     => Access::for(User::findOrFail(2)),
            'manager' => Access::for('Role', 'manager'),
        ];

        // One reset of cached permissions for the whole change
        Access::batch(function () use ($owners, $scenario) {
            foreach ($owners as $owner) {
                foreach (['orders.view', 'orders.approve'] as $rule) {
                    $owner->removeAllow($rule);
                    $owner->removeDeny($rule);
                }
            }

            foreach ($scenario['grants'] as [$who, $effect, $rule, $when]) {
                $owners[$who]->{$effect}($rule, when: $when);
            }
        });

        $result = [];
        foreach (['Ann' => 1, 'Bob' => 2] as $label => $id) {
            // Loaded again: the model that granted a moment ago would do as well, the cache follows every change
            $user = User::findOrFail($id);

            $result[$label] = [
                'listed'   => Order::allowedTo($ability, $user)->orderBy('id')->pluck('id')->all(),
                'opened'   => Order::orderBy('id')->get()->filter(fn (Order $order) => $user->can($ability, $order))->pluck('id')->values()->all(),
                'expected' => $scenario['expected'][$label] ?? 'the article does not say',
            ];
        }

        return Response::json([
            'scenario' => $name,
            'article'  => $scenario['article'],
            'what'     => $scenario['what'],
            'ability'  => $ability instanceof Ability ? $ability->value : $ability,
            'granted'  => $this->stored($owners),
            'result'   => $result,
            'sql'      => Order::allowedTo($ability, User::findOrFail(1))->orderBy('id')->toRawSql(),
        ]);
    }

    /**
     * What the three owners hold for orders now, read back from the database. The table keeps
     * the tree of a condition and no text, so Cond::describe() prints it, the way an editor
     * of an admin panel would show it.
     *
     * @param array<string, \Wnikk\LaravelAccessRules\Administration\OwnerAccess> $owners
     */
    protected function stored(array $owners): array
    {
        $rows = [];

        foreach ($owners as $who => $owner) {
            foreach ($owner->record()?->permission()->with('rule')->get() ?? [] as $permission) {
                if (str_starts_with($permission->rule->guard_name, 'orders.') && $permission->option === null) {
                    $rows[] = [
                        'to' => $who, 'effect' => $permission->permission ? 'allow' : 'deny', 'rule' => $permission->rule->guard_name,
                        'when' => Cond::describe($permission->condition, $permission->rule->resource),
                    ];
                }
            }
        }

        return $rows;
    }

    /**
     * Grants are [owner, "allow" or "deny", rule, condition]. Public: the front page lists the names.
     */
    public function scenarios(): array
    {
        $view = 'orders.view';

        return [
            'reset' => [
                'article' => 'the state of the seeders', 'what' => 'Managers see orders of clients of their teams, except locked ones',
                'grants'  => [
                    ['manager', 'allow', $view, 'order.client.team_id in user.tenant'],
                    ['manager', 'deny', $view, 'order.locked'],
                    ['manager', 'allow', 'orders.approve', 'order.cost <= user.approval_limit && order.user_id != user.id'],
                ],
                'expected' => ['Ann' => [1, 2, 5], 'Bob' => [3]],
            ],
            'cost-and-count' => [
                'article' => 'Example 1', 'what' => 'A column and a count of related records',
                'grants'  => [['ann', 'allow', $view, 'order.cost > 100 && order.items.count < 3']],
                'expected' => ['Ann' => [1, 4, 5]],
            ],
            'same-department' => [
                'article' => 'Example 3', 'what' => 'The record against the user: one line, a different result for each user',
                'grants'  => [['ann', 'allow', $view, 'order.department_id == user.department_id'], ['bob', 'allow', $view, 'order.department_id == user.department_id']],
                'expected' => ['Ann' => [1, 2, 4], 'Bob' => [3, 5, 6]],
            ],
            'approval-limit' => [
                'article' => 'Example 3', 'what' => 'Approve up to your limit, but never your own order', 'ability' => 'orders.approve',
                'grants'  => [['manager', 'allow', 'orders.approve', 'order.cost <= user.approval_limit && order.user_id != user.id']],
                'expected' => ['Ann' => [3, 4, 5]],
            ],
            'not-paris' => [
                'article' => 'Example 4', 'what' => 'Through a relation. Orders 4 and 6 are absent: unknown is not "not Paris"',
                'grants'  => [['ann', 'allow', $view, "order.client.city != 'Paris'"]],
                'expected' => ['Ann' => [1, 3, 5]],
            ],
            'not-paris-or-unknown' => [
                'article' => 'Example 4', 'what' => 'The same, with the unknown city said out loud',
                'grants'  => [['ann', 'allow', $view, "order.client.city == null || order.client.city != 'Paris'"]],
                'expected' => ['Ann' => [1, 3, 4, 5, 6]],
            ],
            'priority' => [
                'article' => 'Example 5', 'what' => 'Inherited permission < inherited prohibition < own permission < own prohibition',
                'grants'  => [
                    ['manager', 'allow', $view, null],
                    ['manager', 'deny', $view, 'order.locked'],
                    ['ann', 'allow', $view, 'order.cost > 400'],
                    ['ann', 'deny', $view, "order.status == 'paid'"],
                ],
                'expected' => ['Ann' => [1, 2, 4, 5, 6], 'Bob' => [1, 2, 3, 5, 6]],
            ],
            'sum-within-limit' => [
                'article' => 'Example 7', 'what' => 'An aggregate against an attribute of the user',
                'grants'  => [['ann', 'allow', $view, 'sum(order.items.price) <= user.approval_limit'], ['bob', 'allow', $view, 'sum(order.items.price) <= user.approval_limit']],
                'expected' => ['Ann' => [1, 3, 4, 5], 'Bob' => [3, 5]],
            ],
            'expensive-item' => [
                'article' => 'Example 7', 'what' => 'An aggregate with a filter',
                'grants'  => [['ann', 'allow', $view, 'exists(order.items, price >= 300)']],
                'expected' => ['Ann' => [2, 4, 6]],
            ],
            'last-week' => [
                'article' => 'Example 7', 'what' => 'Time: a permission that expires by itself',
                'grants'  => [['ann', 'allow', $view, "order.created_at >= ago('7 days')"]],
                'expected' => ['Ann' => [1, 3, 4, 6]],
            ],
            'over-budget' => [
                'article' => 'Example 8', 'what' => 'Arithmetic',
                'grants'  => [['ann', 'allow', $view, 'order.cost * 1.2 > order.budget']],
                'expected' => ['Ann' => [2, 6]],
            ],
            'between' => [
                'article' => 'Example 8', 'what' => 'A range, both ends included',
                'grants'  => [['ann', 'allow', $view, 'order.cost between 100 and 500']],
                'expected' => ['Ann' => [1, 4, 5]],
            ],
            'starts-with' => [
                'article' => 'Example 8', 'what' => 'Text functions, exact on every database',
                'grants'  => [['ann', 'allow', $view, "startsWith(order.number, 'EU-')"]],
                'expected' => ['Ann' => [1, 2, 4, 6]],
            ],
            'lower-city' => [
                'article' => 'Example 8', 'what' => 'lower() on both sides ignores case',
                'grants'  => [['ann', 'allow', $view, "lower(order.client.city) == 'berlin'"]],
                'expected' => ['Ann' => [1, 3, 5]],
            ],
            'tenants' => [
                'article' => 'Example 9', 'what' => 'Teams as tenants: "user.tenant" lists the teams the user inherits from',
                'grants'  => [['manager', 'allow', $view, 'order.client.team_id in user.tenant']],
                'expected' => ['Ann' => [1, 2, 5], 'Bob' => [3, 4]],
            ],
            'category-tree' => [
                'article' => 'Example 10', 'what' => 'Electronics and everything under it',
                'grants'  => [['ann', 'allow', $view, "order.category_id in belowOrSelf('category.name', 'Electronics')"]],
                'expected' => ['Ann' => [1, 2, 4, 5]],
            ],
            'no-restricted-goods' => [
                'article' => 'Example 11', 'what' => 'Many-to-many',
                'grants'  => [['ann', 'allow', $view, 'not exists(order.products, restricted)']],
                'expected' => ['Ann' => [1, 3, 5, 6]],
            ],
            'wholesale' => [
                'article' => 'Example 11', 'what' => 'A column of the pivot table',
                'grants'  => [['ann', 'allow', $view, 'exists(order.products, pivot.quantity >= 5)']],
                'expected' => ['Ann' => [2, 6]],
            ],
            'pivot-sum' => [
                'article' => 'Example 11', 'what' => 'An aggregate over a pivot column',
                'grants'  => [['ann', 'allow', $view, 'sum(order.products.pivot.quantity) >= 3']],
                'expected' => ['Ann' => [1, 2, 6]],
            ],
            'working-hours' => [
                'article' => 'Example 12', 'what' => 'The environment. Outside of working hours the database gets "0 = 1" and reads no rows', 'ability' => 'orders.approve',
                'grants'  => [['manager', 'allow', 'orders.approve', 'env.weekday in [1, 2, 3, 4, 5] && env.hour between 9 and 18']],
                'expected' => [],
            ],
            'enum-ability' => [
                'article' => 'Example 13', 'what' => 'The name of the rule as a backed enum', 'ability' => Ability::OrdersView,
                'grants'  => [['ann', 'allow', Ability::OrdersView, 'order.cost > 100']],
                'expected' => ['Ann' => [1, 2, 4, 5, 6]],
            ],
            'builder' => [
                'article' => 'Example 13', 'what' => 'A condition built in code gives the same tree as its text',
                'grants'  => [['ann', 'allow', $view, Cond::all(Cond::attr('order.cost')->gt(100), Cond::count('order.items')->lt(3))]],
                'expected' => ['Ann' => [1, 4, 5]],
            ],
        ];
    }
}
