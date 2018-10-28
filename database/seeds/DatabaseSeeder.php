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
        $this->call(MerchantsTableSeeder::class);
        $this->call(PurchasersTableSeeder::class);
        $this->call(CommoditiesTableSeeder::class);
    }
}
