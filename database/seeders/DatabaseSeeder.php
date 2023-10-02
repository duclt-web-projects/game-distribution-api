<?php

namespace Database\Seeders;

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
        $this->call([
            GameSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            CategoryGameSeeder::class,
            GameTagSeeder::class,
            UserSeeder::class,
        ]);
    }
}
