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
        for ($i=0; $i < 10; $i++) { 
	    

              DB::table('products')->insert([
               'name' => str_shuffle('qwertyuiopasdfghjklzxcvbnm')[0],
                'brand' =>str_shuffle('qwertyuiopasdfghjklzxcvbnm')[0],
                'weight' => rand(10,50),
                'weight_type'=>'kg'
        ]);
    	}
    }
}
