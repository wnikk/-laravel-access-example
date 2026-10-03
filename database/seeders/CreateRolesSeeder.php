<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Wnikk\LaravelAccessRules\Facades\Access;

class CreateRolesSeeder extends Seeder
{
    /**
     * Owners without a model: roles and teams. Access::for() addresses one by type and id.
     */
    public function run(): void
    {
        Access::batch(function () {
            $this->root();
            $this->manager();
            $this->guest();

            // Teams are tenants, see 'tenant_types' in config/access.php. A user that inherits
            // from a team gets its id into the list "user.tenant".
            Access::for('Team', 1)->create('North');
            Access::for('Team', 2)->create('South');
        });
    }

    /**
     * Everything the first tutorial checks, examples 1-7.
     */
    protected function root(): void
    {
        $root = Access::for('Role', 'root');
        $root->create('RootAdmin role');

        $root->allow('example1.viewAny');
        $root->allow('example2.view');

        $root->allow('example3.update', 'name');
        $root->allow('example3.update', 'email');
        $root->allow('example3.update', 'password');

        foreach (['viewAny', 'view', 'create', 'update', 'delete'] as $action) {
            $root->allow($action);
            $root->allow('Examples.Example5.'.$action);
        }

        // For all news it would be: $root->allow('example6.update');
        $root->allow('example6.update.self');

        // The policy of 2.x as one line: news of the last two days
        $root->allow('Example7News.update', when: "news.created_at >= ago('48 hours')");

        $root->allow('system.access.manage');
    }

    /**
     * The starting state of the shop. /example9 replaces these permissions with one scenario
     * of the tutorial at a time, and /example9/reset brings this state back.
     */
    protected function manager(): void
    {
        $manager = Access::for('Role', 'manager');
        $manager->create('Managers');

        $manager->allow('orders.view', when: 'order.client.team_id in user.tenant');
        $manager->deny('orders.view', when: 'order.locked');
        $manager->allow('orders.approve', when: 'order.cost <= user.approval_limit && order.user_id != user.id');
        $manager->allow('orders.export', 'csv');
    }

    /**
     * Requests without a user are checked as this owner, see 'guest' in config/access.php.
     */
    protected function guest(): void
    {
        $guest = Access::for('Role', 'guest');
        $guest->create('Guests');

        $guest->allow('catalog.view', when: 'not product.restricted');
    }
}
