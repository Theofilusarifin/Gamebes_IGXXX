<?php

use App\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/4-services.csv";
        $model = Service::class;
        include("csv-reader.php");
    }
}
