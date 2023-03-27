<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Wnikk\LaravelAccessRules\AccessRules;

class CreateRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // example #1 - route middleware
        AccessRules::newRule('example1.viewAny', 'View all users on example1');

        // example #2 - check in action
        AccessRules::newRule('example2.view', 'View data of user on example2');

        // example #3 - check on action options
        AccessRules::newRule([
            'guard_name' => 'example3.update',
            'title' => 'Changing different user data on example3',
            'options' => 'required|in:name,email,password'
        ]);

        // example #4 - global resource
        AccessRules::newRule('viewAny', 'Global rule "viewAny" for example4');
        AccessRules::newRule('view', 'Global rule "view" for example4');
        AccessRules::newRule('create', 'Global rule "create" for example4');
        AccessRules::newRule('update', 'Global rule "update" for example4');
        AccessRules::newRule('delete', 'Global rule "delete" for example4');

        // example #5 - resource for controller
        AccessRules::newRule('Example5News.viewAny', 'Rule for one Controller his action "viewAny" example5');
        AccessRules::newRule('Example5News.view', 'Rule for one Controller his action "view" example5');
        AccessRules::newRule('Example5News.create', 'Rule for one Controller his action "create" example5');
        AccessRules::newRule('Example5News.update', 'Rule for one Controller his action "update" example5');
        AccessRules::newRule('Example5News.delete', 'Rule for one Controller his action "delete" example5');

        // example #6 - magic self
        AccessRules::newRule(
            'example6.update',
            'Rule that allows edit all news',
        'An example of how to use a magic suffix ".self" on example6'
        );
        AccessRules::newRule('example6.update.self', 'Rule that allows edit only where user is author');

        // example #7 - Policy
        AccessRules::newRule('Example7News.create', 'Rule for one Model her event "create" example7');
        AccessRules::newRule('Example7News.update', 'Rule for one Model her event "update" example7');
        AccessRules::newRule('Example7News.delete', 'Rule for one Model her event "delete" example7');
        AccessRules::newRule('Example7News.forceDelete', 'Rule for one Model her event "forceDelete" example7');

        // Final example, add control to the Access user interface
        $id = AccessRules::newRule('UserRules.index', 'View all rules, permits and inheritance');
        AccessRules::newRule('UserRules.rules', 'Working with Rules', null, $id, 'required|in:index,store,update,destroy');
        AccessRules::newRule('UserRules.roles', 'Working with Roles', null, $id, 'required|in:index,store,update,destroy');
        AccessRules::newRule('UserRules.inherit', 'Working with Inherit', null, $id, 'required|in:index,store,destroy');
        AccessRules::newRule('UserRules.permission', 'Working with Permission', null, $id, 'required|in:index,update');
    }
}
