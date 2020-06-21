<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;
class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon::create(2020,mt_rand(1,5),mt_rand(1,31));
        for ($i= 1; $i <=100 ; $i++) { 
           DB::table('stocks')->insert([
               "user_id" => 1,
               "product_id" => $i,
               "quantity" => mt_rand(0,500),
               "price" => mt_rand(10,100),
               "product_source" => 'main',
               "last_purchased_at" => $date,
               "created_at" => $date,
               "updated_at" => $date,
           ]);
        }
    }
}
