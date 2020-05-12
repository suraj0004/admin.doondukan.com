<?php

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\TempProduct;

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
	    	TempProduct::create([
	            'name' => str_shuffle('ABCDE'),
                'user_id' => 1,
	            'brand' =>str_shuffle('ABCDE'),
	            'weight' => rand(10,50),
	            'weight_type'=>'kg'
	        ]);
    	}
    }
}
