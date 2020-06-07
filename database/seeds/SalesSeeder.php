<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
        for($i=1; $i<=1000; $i++){

            if($i>900){
                $date = Carbon::create(2019,mt_rand(1,12),mt_rand(1,31));
            }
            else if($i<100){
                $date = Carbon::create(2020,6,mt_rand(1,5));
            }
            else{
                $date = Carbon::create(2020,mt_rand(1,5),mt_rand(1,31));
            }
            $product_id = mt_rand(1,100);
            DB::table('sales')->insert([
              "user_id" => 1,
              "product_id" => $product_id,
              "bill_id" => $i,
              "quantity" => mt_rand(1,10),
              "price" => $product_id*10,
              "product_source" => "main",
              "created_at" =>  $date
            ]);
        }
    }
}
