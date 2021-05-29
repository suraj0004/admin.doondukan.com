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
            'email' => 'shop@doondukan.com',
            'phone' => 9876543210,
            'password' => Hash::make('123456'),
            'role' => 'SHOPKEEPER',
            'created_at' => Carbon::create(2021,1,1),
            'updated_at' => Carbon::create(2021,1,1)
        ]);

        DB::table('stores')->insert([
            'user_id' => 1,
            'name' => 'shop',
            'slug' => "shop",
            'email' => 'shop@doondukan.com',
            'mobile' => 9876543210,
            'address' => 'address',
            'about' => 'about',
            'logo' => '',
            'registration_date' =>  Carbon::create(2021,1,1),
            'valid_upto' => Carbon::create(2022,1,1),
            'created_at' => Carbon::create(2021,1,1),
            'updated_at' => Carbon::create(2021,1,1),
            'open_at' => "10:00:00",
            'close_at' => "20:00:00",
        ]);

        DB::table('users')->insert([
            'name' => 'user ',
            'email' => 'user@gmail.com',
            'phone' => 8954836965,
            'password' => Hash::make('123456'),
            'role' => 'USER',
            'created_at' => Carbon::create(2021,1,1)
        ]);

        DB::table('admins')->insert([
            'name' => 'Admin',
            'email' => 'admin@doondukan.com',
            'password' => Hash::make('admin'),
            'created_at' => Carbon::create(2021,1,1),
            'updated_at' => Carbon::create(2021,1,1)

        ]);
    }
}
