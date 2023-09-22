<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tags')->truncate();

        $tags = file_get_contents(base_path('database/files/tags.json'));
        $tags = json_decode($tags);

        $data = [];

        foreach ($tags as $tag) {
            $data[] = [
                'name' => $tag,
                'slug' => Str::slug($tag),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('tags')->insert($data);
    }
}
