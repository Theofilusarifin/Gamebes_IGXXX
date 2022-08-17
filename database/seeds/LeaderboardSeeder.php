<?php

use App\Leaderboard;
use Illuminate\Database\Seeder;

class LeaderboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/20-leaderboards.csv";
        $model = Leaderboard::class;
        include("csv-reader.php");
    }
}
