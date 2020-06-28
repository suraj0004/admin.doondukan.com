<?php

use Illuminate\Database\Seeder;

class CategotySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i <= 10; $i++) { 
	
            DB::table('categories')->insert([
              'category_name' => "category-".$i,
         ]);
      }
    }
}
