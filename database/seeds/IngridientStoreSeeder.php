<?php

use App\IngridientStore;
use Illuminate\Database\Seeder;

class IngridientStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/1-ingridient_stores.csv";
        $model = IngridientStore::class;
        include("csv-reader.php");
    }
}
