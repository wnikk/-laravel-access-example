<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Wnikk\LaravelAccessRules\Facades\Access;

/**
 * Example 15 of the bench, example 18 of docs/tutorial-abac-step-by-step.md: polymorphic relations.
 *
 * A tag hangs on a product and on a category alike (morphToMany, a morph map in
 * App\Providers\AppServiceProvider). A condition walks the relation like any other, and the morph
 * type travels in the subquery because Eloquent builds it. The category Laptops is the proof: it is
 * tagged "sale" and shares its id with the Battery pack, which never appears as "on sale".
 *
 *     /example15                what can be tried
 *     /example15/on-sale        apply one scenario, see the result and the SQL
 *     /example15/reset          take the permissions of this example away from user 3
 */
class Example15Controller extends Controller
{
    public function index()
    {
        return Response::json(array_map(
            fn (array $scenario, string $name): array => ['try' => url('/example15/'.$name), 'what' => $scenario['what']],
            $this->scenarios(),
            array_keys($this->scenarios()),
        ));
    }

    public function show(string $name)
    {
        $scenario = $this->scenarios()[$name] ?? abort(404, 'Unknown scenario, see /example15');
        $model    = $scenario['model'];
        $ability  = $model === Order::class ? 'orders.view' : 'products.view';

        // User 3 holds nothing else, so what the list shows is the condition alone. Ann would add
        // what she inherits from the role "manager" to the orders.
        $user = User::findOrFail(3);

        // Through the trait: user 3 has no owner row yet, and $user->addPermission() creates it.
        // Access::for($user) addresses an owner that exists and does not create one.
        Access::batch(function () use ($user, $scenario, $ability) {
            $user->remPermission('products.view');
            $user->remPermission('orders.view');

            if ($scenario['when'] !== null) {
                $user->addPermission($ability, when: $scenario['when']);
            }
        });

        $user = User::findOrFail(3);

        return Response::json([
            'scenario' => $name,
            'what'     => $scenario['what'],
            'when'     => $scenario['when'],
            'listed'   => $model::allowedTo($ability, $user)->orderBy('id')->pluck($model === Product::class ? 'name' : 'number', 'id'),
            'opened'   => $model::orderBy('id')->get()->filter(fn ($record) => $user->can($ability, $record))->pluck('id')->values()->all(),
            'expected' => $scenario['expected'],
            'sql'      => $scenario['when'] === null ? null : $model::allowedTo($ability, $user)->toRawSql(),
        ]);
    }

    /**
     * Products: 1 Phone (sale, new), 2 Charger (sale), 3 Battery pack (new). Category 3 Laptops: sale.
     */
    public function scenarios(): array
    {
        return [
            'reset' => ['what' => 'Take the permissions of this example away from user 3', 'model' => Product::class, 'when' => null, 'expected' => []],
            'on-sale' => [
                'what'  => 'Tagged "sale"',
                'model' => Product::class, 'when' => "exists(product.tags, name == 'sale')", 'expected' => [1, 2],
            ],
            'not-on-sale' => [
                'what'  => 'Not tagged "sale"',
                'model' => Product::class, 'when' => "not exists(product.tags, name == 'sale')", 'expected' => [3],
            ],
            'orders-with-sale' => [
                'what'  => 'From the other side: orders that hold a product on sale (the permission goes to orders.view)',
                'model' => Order::class, 'when' => "exists(order.products, exists(tags, name == 'sale'))", 'expected' => [1, 4, 6],
            ],
        ];
    }
}
