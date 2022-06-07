<?php

use App\IngridientStore;
use App\MachineStore;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        $this->call(IngridientStoreSeeder::class, false, ["path" => "database/data/1-ingridient_store.csv", "model" => IngridientStore::class]);
        $this->call(MachineStoreSeeder::class, false, ["path" => "database/data/2-machine_store.csv", "model" => MachineStore::class]);
        $this->call(TransportStoreSeeder::class, false, ["path" => "database/data/1-ingridient_store.csv", "model" => IngridientStore::class]);
        $this->call(ServiceSeeder::class, false, ["path" => "database/data/1-ingridient_store.csv", "model" => IngridientStore::class]);
        $this->call(TerritorySeeder::class, false, ["path" => "database/data/1-ingridient_store.csv", "model" => Terr::class]);


    }
}
