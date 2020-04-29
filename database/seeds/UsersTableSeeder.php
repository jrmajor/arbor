<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'maksiuP',
            'email' => 'maksiu@example.com',
            'password' => bcrypt('password'),
            'permissions' => 4,
        ]);
    }
}
