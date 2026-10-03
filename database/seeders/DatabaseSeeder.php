<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Wnikk\LaravelAccessRules\Facades\Access;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     *   php artisan migrate:fresh --seed
     *
     * Ann (user 1, password "12345") holds the role "root" of the first tutorial and works in
     * team North. Bob (user 2, password "password") works in team South. Both are managers of the
     * shop. Users 3-5 hold nothing at all.
     */
    public function run(): void
    {
        $this->call([
            CreateUserSeeder::class,
            CreateRulesSeeder::class,
            CreateRolesSeeder::class,
            NewsTableSeeder::class,
            ShopSeeder::class,
            CatalogSeeder::class,
        ]);

        Access::batch(function () {
            $ann = User::findOrFail(1);
            $ann->inheritPermissionFrom('Role', 'root');
            $ann->inheritPermissionFrom('Role', 'manager');
            $ann->inheritPermissionFrom('Team', 1);

            $bob = User::findOrFail(2);
            $bob->inheritPermissionFrom('Role', 'manager');
            $bob->inheritPermissionFrom('Team', 2);
        });
    }
}
