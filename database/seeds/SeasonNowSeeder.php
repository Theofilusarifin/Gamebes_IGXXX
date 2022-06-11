<?php

use App\SeasonNow;
use Illuminate\Database\Seeder;

class SeasonNowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/14-season_now.csv";
        $model = SeasonNow::class;
        include("csv-reader.php");
    }
}
