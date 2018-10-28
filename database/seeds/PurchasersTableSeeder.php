<?php

use Illuminate\Database\Seeder;
use App\Purchaser;
use Illuminate\Support\Facades\DB;

class PurchasersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Purchaser::truncate();

        $pass = Hash::make('zjffzxfk');

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $acct = random_int(10000000, 99999999);
            $ali = $faker->firstName();

            while (!$this->check($acct, 'account')) {
                $acct = random_int(10000000, 99999999);
            }

            while (!$this->check($ali, 'alias')) {
                $ali = $faker->firstName();
            }

            $m = round($faker->randomFloat() * 10,2);
            while ($m < 1000 || $m > 50000) {
                $m = round($faker->randomFloat() * 10,2);
            }

            Purchaser::create([
                'account' => $acct,
                'alias' => $ali,
                'password' => $pass,
                'blance' => $m,
            ]);
        }
    }

    public function check($data, $col) {
        $datas = DB::table('purchasers')->pluck($col);

        foreach ($datas as $d) {
            if ($data == $d)
                return false;
        }
        return true;
    }
}
