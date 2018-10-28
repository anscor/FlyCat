<?php

use Illuminate\Database\Seeder;
use App\Commodity;
use App\Merchant;
use Illuminate\Support\Facades\DB;

class CommoditiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Commodity::truncate();

        $merchants = DB::table('merchants')->pluck('id');

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $t = $faker->randomNumber();
            if ($t == 0 || $t > 100) {
                $i--;
                continue;
            }

            $m = round($faker->randomFloat(),2);
            while ($m < 10 || $m > 1000) {
                $m = round($faker->randomFloat(),2);
            }

            Commodity::create([
                'count' => $t,
                'price' => $m,
                'name' => $faker->name(),
                'owner' => $faker->randomElement($merchants),
            ]);
        }
    }
}
