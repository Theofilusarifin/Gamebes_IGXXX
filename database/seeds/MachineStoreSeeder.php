<?php

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
        include("csv-reader.php");
    }
}
