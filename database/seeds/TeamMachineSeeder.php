<?php

use App\TeamMachine;
use Illuminate\Database\Seeder;

class TeamMachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/16-team_machines.csv";
        $model = TeamMachine::class;
        include("csv-reader.php");
    }
}
