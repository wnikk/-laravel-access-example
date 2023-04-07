<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Wnikk\LaravelAccessRules\AccessRules;

class CreateRootAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acr = new AccessRules;
        $acr->newOwner('Role', 'root', 'RootAdmin role');

        // For example #1
        $acr->addPermission('example1.viewAny');

        // For example #2
        $acr->addPermission('example2.view');

        // For example #3
        $acr->addPermission('example3.update', 'name');
        $acr->addPermission('example3.update', 'email');
        $acr->addPermission('example3.update', 'password');

        // For example #4
        $acr->addPermission('viewAny');
        $acr->addPermission('view');
        $acr->addPermission('create');
        $acr->addPermission('update');
        $acr->addPermission('delete');

        // For example #5
        $acr->addPermission('Examples.Example5.viewAny');
        $acr->addPermission('Examples.Example5.view');
        $acr->addPermission('Examples.Example5.create');
        $acr->addPermission('Examples.Example5.update');
        $acr->addPermission('Examples.Example5.delete');

        // For example #6
        //For all - $acr->addPermission('example6.update');
        $acr->addPermission('example6.update.self');

        // For example #7
        $acr->addPermission('Example7News.test');

        // For final example
        $acr->addPermission('Examples.UserRules.index');
        $acr->addPermission('Examples.UserRules.rules');
        $acr->addPermission('Examples.UserRules.rules', 'index');
        $acr->addPermission('Examples.UserRules.rules', 'store');
        $acr->addPermission('Examples.UserRules.rules', 'update');
        $acr->addPermission('Examples.UserRules.rules', 'destroy');
        $acr->addPermission('Examples.UserRules.roles');
        $acr->addPermission('Examples.UserRules.roles', 'index');
        $acr->addPermission('Examples.UserRules.roles', 'store');
        $acr->addPermission('Examples.UserRules.roles', 'update');
        $acr->addPermission('Examples.UserRules.roles', 'destroy');
        $acr->addPermission('Examples.UserRules.inherit');
        $acr->addPermission('Examples.UserRules.inherit', 'index');
        $acr->addPermission('Examples.UserRules.inherit', 'store');
        $acr->addPermission('Examples.UserRules.inherit', 'destroy');
        $acr->addPermission('Examples.UserRules.permission');
        $acr->addPermission('Examples.UserRules.permission', 'index');
        $acr->addPermission('Examples.UserRules.permission', 'update');

        $acr->clearAllCachedPermissions();
    }
}
