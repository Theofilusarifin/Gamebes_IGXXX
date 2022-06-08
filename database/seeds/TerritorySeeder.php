<?php

use App\Territory;
use Illuminate\Database\Seeder;

class TerritorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/5-territories.csv";
        $model = Territory::class;
        include("csv-reader.php");
    }
}
