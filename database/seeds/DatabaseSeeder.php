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
        $this->call(IngridientStoreSeeder::class, false);
        $this->call(MachineStoreSeeder::class, false, );
        $this->call(TransportStoreSeeder::class, false);
        $this->call(ServiceSeeder::class, false);
        $this->call(TerritorySeeder::class, false);
        $this->call(InvestationSeeder::class, false);
        $this->call(TeamSeeder::class, false);
        $this->call(UserSeeder::class, false);
        $this->call(IngridientSeeder::class, false);
        $this->call(MachineSeeder::class, false);
        $this->call(ProductSeeder::class, false);
        $this->call(TransportSeeder::class, false);

    }
}
