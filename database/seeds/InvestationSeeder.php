<?php

use App\Investation;
use Illuminate\Database\Seeder;

class InvestationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/6-investations.csv";
        $model = Investation::class;
        include("csv-reader.php");
    }
}
