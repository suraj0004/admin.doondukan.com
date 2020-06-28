<?php

use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i <= 10; $i++) { 
	
            DB::table('brands')->insert([
              'brand_name' => "Brand-".$i,
         ]);
      }
    }
}
