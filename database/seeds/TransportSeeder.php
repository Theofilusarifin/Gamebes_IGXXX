<?php

use App\Transport;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/12-transports.csv";
        $model = Transport::class;
        include("csv-reader.php");
    }
}
