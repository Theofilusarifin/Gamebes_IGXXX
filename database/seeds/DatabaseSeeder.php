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

        $this->call(IngridientStoreSeeder::class, false);
        $this->call(MachineStoreSeeder::class, false, );
        $this->call(TransportStoreSeeder::class, false);
        $this->call(ServiceSeeder::class, false);
        $this->call(TerritorySeeder::class, false);
    }
}
