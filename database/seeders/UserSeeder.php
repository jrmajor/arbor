<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'username' => 'jrmajor',
            'email' => 'jrmajor@example.com',
            'password' => bcrypt('password'),
            'permissions' => 4,
        ]);
    }
}
