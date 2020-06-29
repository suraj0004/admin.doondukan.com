<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
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
            'phone' => 9876543210,
            'password' => Hash::make('123456'),
            'role' => 'user',
            'created_at' => Carbon::create(2019,1,1)
        ]);

        DB::table('admins')->insert([
            'name' => 'Admin',
            'email' => 'admin@disc-in.com',
            'password' => Hash::make('admin'),
            'created_at' => Carbon::create(2019,1,1),
            'updated_at' => Carbon::create(2019,1,1)

        ]);
    }
}
