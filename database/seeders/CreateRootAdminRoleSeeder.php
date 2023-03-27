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
        $acr->addPermission('Example5News.viewAny');
        $acr->addPermission('Example5News.view');
        $acr->addPermission('Example5News.create');
        $acr->addPermission('Example5News.update');
        $acr->addPermission('Example5News.delete');

        // For example #6
        //For all - $acr->addPermission('example6.update');
        $acr->addPermission('example6.update.self');

        // For example #7
        $acr->addPermission('Example7News.create');
        $acr->addPermission('Example7News.update');
        $acr->addPermission('Example7News.delete');
        $acr->addPermission('Example7News.forceDelete');

        // For final example
        $acr->addPermission('UserRules.index');
        $acr->addPermission('UserRules.rules', 'index');
        $acr->addPermission('UserRules.rules', 'store');
        $acr->addPermission('UserRules.rules', 'update');
        $acr->addPermission('UserRules.rules', 'destroy');
        $acr->addPermission('UserRules.roles', 'index');
        $acr->addPermission('UserRules.roles', 'store');
        $acr->addPermission('UserRules.roles', 'update');
        $acr->addPermission('UserRules.roles', 'destroy');
        $acr->addPermission('UserRules.inherit', 'index');
        $acr->addPermission('UserRules.inherit', 'store');
        $acr->addPermission('UserRules.inherit', 'destroy');
        $acr->addPermission('UserRules.permission', 'index');
        $acr->addPermission('UserRules.permission', 'update');
    }
}
