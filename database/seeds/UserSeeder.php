<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/8-users batch 2.csv";
        $model = User::class;
        include("csv-reader.php");
    }
}
