<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $discount_type = ["rupees","percentage"];
        $status = ["paid","unpaid"];
        for($i=1; $i<=1000; $i++){

            $name = str_shuffle("suraj upadhyay");
            DB::table('bills')->insert([
              "user_id" => 1,
              "customer_name" => $name,
              "customer_email" => $name."@gmail.com",
              "customer_mobile" => mt_rand(9111111111,9999999999),
              "discount" => mt_rand(0,100),
              "discount_type" => shuffle($discount_type)?$discount_type[0]:"rupees" ,
              "status" => shuffle($status)?$status[0]:"rupees" ,
              "created_at" => $now
            ]);
        }
    }
}
