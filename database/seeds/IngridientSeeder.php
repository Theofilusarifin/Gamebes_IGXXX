<?php

use App\Ingridient;
use Illuminate\Database\Seeder;

class IngridientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/9-ingridients.csv";
        $model = Ingridient::class;
        include("csv-reader.php");
    }
}
