<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(PermissionSeeder::class);

        $this->call(CountrySeeder::class);
        $this->call(CitySeeder::class);
        $this->call(GeoObjectSeeder::class);

        $this->call(BedTypeSeeder::class);
        $this->call(RoomTypeSeeder::class);
        $this->call(ApartmentTypeSeeder::class);

        $this->call(BedSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(ApartmentSeeder::class);
    }
}
