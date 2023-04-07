<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Wnikk\LaravelAccessRules\AccessRules;

class AddRoleToAllUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $all = User::all();
        foreach ($all as $one) $one->inheritPermissionFrom('Role', 'root');

        // or
        // $acr = new AccessRules;
        // $acr->setOwner('Role', 'root');
        // foreach ($all as $one) $one->inheritPermissionFrom($acr);

        // or
        // $mainUser = User::find(1);
        // foreach ($all as $one) $one->inheritPermissionFrom($mainUser);

        $acr = new AccessRules;
        $acr->clearAllCachedPermissions();
    }
}
