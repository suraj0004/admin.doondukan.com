<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ProductsTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i <= 100; $i++) { 
	    

              DB::table('products')->insert([
                'name' => "Product-".$i,
                'brand_id' =>  mt_rand(1,10),
                "category_id" => mt_rand(1,20),
                'weight' => rand(10,50),
                'weight_type'=>'kg'
        ]);
    	}
    }
}
