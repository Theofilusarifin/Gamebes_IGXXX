<?php

use App\MachineStore;
use Illuminate\Database\Seeder;

class MachineStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/2-machine_stores.csv";
        $model = MachineStore::class;
        include("csv-reader.php");
    }
}
