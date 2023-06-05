<?php

namespace Database\Seeders\Performance;

use App\Models\GeoObject;
use Illuminate\Database\Seeder;

class GeoobjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 100): void
    {
        Geoobject::factory($count)->create();
    }
}
