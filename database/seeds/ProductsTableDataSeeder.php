<?php

use Illuminate\Database\Seeder;
use App\Models\Product;

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
	    	Product::create([
	            'name' => str_shuffle('ABCDE'),
	            'brand' =>str_shuffle('ABCDE'),
	            'weight' => rand(10,50),
	            'weight_type'=>'kg'
	        ]);
    	}
    }
}
