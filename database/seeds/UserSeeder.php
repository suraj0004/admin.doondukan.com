<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('users')->insert([
            'name' => 'shop keeper',
            'email' => 'shop@disc-in.com',
            'phone' => 1234567890,
            'password' => Hash::make('123456'),
            'role' => 'user',
        ]);
    }
}
