<?php

use App\MachineCombination;
use Illuminate\Database\Seeder;

class MachineCombinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/13-machine_combinations";
        $model = MachineCombination::class;
        include("csv-reader.php");
    }
}
