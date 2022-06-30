<?php

use App\Answer;
use Illuminate\Database\Seeder;

class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = "database/data/18-answers.csv";
        $model = Answer::class;
        include("csv-reader.php");
    }
}
