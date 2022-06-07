<?php

use Illuminate\Database\Seeder;

class IngridientStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($path, $model)
    {
        include("csv-reader.php");
    }
}
