<?php

namespace Database\Seeders;

use Database\Seeders\data\userSeeder;
use Illuminate\Database\Seeder;

class data extends Seeder
{

    public function run(): void
    {
        $this->call(userSeeder::class);
    }
}
