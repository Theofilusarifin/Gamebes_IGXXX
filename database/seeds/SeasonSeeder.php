<?php

use App\Season;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/15-seasons.csv";
        $model = Season::class;
        include("csv-reader.php");
    }
}
