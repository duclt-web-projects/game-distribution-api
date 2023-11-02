<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->truncate();

        $categories = file_get_contents(base_path('database/files/categories.json'));
        $categories = json_decode($categories, 1);

        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'name' => $category['label'],
                'slug' => Str::slug($category['label']),
                'icon' => $category['icon'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('categories')->insert($data);
    }
}
