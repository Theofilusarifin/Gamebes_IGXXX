<?php

use App\TransportStore;
use Illuminate\Database\Seeder;

class TransportStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/3-transport_stores.csv";
        $model = TransportStore::class;
        include("csv-reader.php");
    }
}
