<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Test user 1',
            'email' => 'root@mail.com',
            'password' => Hash::make('12345'),
        ]);
        DB::table('users')->insert([
            'id' => 2,
            'name' => 'Test user 2',
            'email' => 'test@mail.com',
            'password' => Hash::make('password'),
        ]);
        DB::table('users')->insert([
            'name' => 'Test user 3',
            'email' => Str::random(10).'@mail.com',
            'password' => Hash::make(Str::random(10)),
        ]);
        DB::table('users')->insert([
            'name' => 'Test user 4',
            'email' => Str::random(10).'@mail.com',
            'password' => Hash::make(Str::random(10)),
        ]);
        DB::table('users')->insert([
            'name' => 'Test user 5',
            'email' => Str::random(10).'@mail.com',
            'password' => Hash::make(Str::random(10)),
        ]);
    }
}
