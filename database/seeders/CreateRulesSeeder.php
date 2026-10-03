<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Wnikk\LaravelAccessRules\Facades\Access;

class CreateRulesSeeder extends Seeder
{
    /**
     * A rule is a name that code asks about. It has to exist before anybody can hold it.
     */
    public function run(): void
    {
        // One reset of cached permissions after all rules instead of one per rule
        Access::batch(function () {
            $this->rulesOfTheFirstTutorial();
            $this->rulesOfTheShop();
            $this->rulesOfTheSystem();
        });
    }

    /**
     * Examples 1-7, docs/tutorial-basic-step-by-step.md: RBAC as it was in 2.x.
     */
    protected function rulesOfTheFirstTutorial(): void
    {
        // example #1 - route middleware
        Access::newRule('example1.viewAny', 'View all users on example1');

        // example #2 - check in action
        Access::newRule('example2.view', 'View data of user on example2');

        // example #3 - check on action options
        Access::newRule(
            'example3.update',
            'Changing different user data on example3',
            options: 'required|in:name,email,password'
        );

        // example #4 - global resource
        Access::newRule('viewAny', 'Global rule "viewAny" for example4');
        Access::newRule('view', 'Global rule "view" for example4');
        Access::newRule('create', 'Global rule "create" for example4');
        Access::newRule('update', 'Global rule "update" for example4');
        Access::newRule('delete', 'Global rule "delete" for example4');

        // example #5 - resource for controller
        foreach (['viewAny', 'view', 'create', 'update', 'delete'] as $action) {
            Access::newRule('Examples.Example5.'.$action, 'Rule for one Controller his action "'.$action.'" example5');
        }

        // example #6 - magic self
        Access::newRule(
            'example6.update',
            'Rule that allows edit all news',
            'An example of how to use a magic suffix ".self" on example6'
        );
        Access::newRule('example6.update.self', 'Rule that allows edit only where user is author');

        // example #7 - a policy in 2.x, a condition in 3.x: "resource" names the model the rule is about
        Access::newRule('Example7News.update', 'Edit fresh news', resource: 'news');
    }

    /**
     * Examples 8 and up, docs/tutorial-abac-step-by-step.md: permissions that look at data.
     */
    protected function rulesOfTheShop(): void
    {
        $orders = Access::newRule('orders', 'Orders', 'Back office of the shop');

        Access::newRule('orders.view', 'View orders', parentId: $orders, resource: 'order');
        Access::newRule('orders.approve', 'Approve orders', parentId: $orders, resource: 'order');

        // A condition of the rule itself applies to everybody who holds it, on top of their own conditions
        Access::newRule('orders.export', 'Export orders', parentId: $orders, options: 'required|in:csv,pdf', resource: 'order', when: 'not order.locked');

        Access::newRule('catalog.view', 'View the catalogue', 'Held by the guest role', resource: 'product');
        Access::newRule('products.view', 'View products', 'Example 18: polymorphic relations', resource: 'product');
    }

    protected function rulesOfTheSystem(): void
    {
        $system = Access::newRule('system', 'System', 'Administration of the application itself');
        $access = Access::newRule('system.access', 'Access control', parentId: $system);

        // The Gate "manage-access" of App\Providers\AppServiceProvider resolves to this rule.
        // It guards the screens that hand out permissions: explain, lint, XACML.
        Access::newRule(
            'system.access.manage',
            'Manage access control',
            'Opens the screens that show and exchange permissions',
            $access
        );
    }
}
