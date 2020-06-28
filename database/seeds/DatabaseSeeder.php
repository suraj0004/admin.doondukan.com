<?php

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
           [
                UserSeeder::class,
                BrandSeeder::class,
                CategotySeeder::class,
                ProductsTableDataSeeder::class,
                // StockSeeder::class,
                // SalesSeeder::class,
                // BillSeeder::class,
          ]
        );
    }
}
