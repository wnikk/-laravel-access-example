<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUserSeeder extends Seeder
{
    /**
     * Ann and Bob are the two users of the ABAC tutorial: the department and the approval
     * limit are attributes that conditions read as "user.department_id" and "user.approval_limit".
     * Users 3-5 hold nothing, so a refusal is always one sign-in away.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Ann',
            'email' => 'root@mail.com',
            'password' => Hash::make('12345'),
            'department_id' => 1,
            'approval_limit' => 500,
        ]);
        DB::table('users')->insert([
            'id' => 2,
            'name' => 'Bob',
            'email' => 'test@mail.com',
            'password' => Hash::make('password'),
            'department_id' => 2,
            'approval_limit' => 100,
        ]);

        foreach ([3, 4, 5] as $id) {
            DB::table('users')->insert([
                'id' => $id,
                'name' => 'Test user '.$id,
                'email' => Str::random(10).'@mail.com',
                'password' => Hash::make(Str::random(10)),
            ]);
        }
    }
}
