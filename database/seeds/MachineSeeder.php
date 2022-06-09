<?php

use App\Machine;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/10-machines.csv";
        $model = Machine::class;
        include("csv-reader.php");
    }
}
