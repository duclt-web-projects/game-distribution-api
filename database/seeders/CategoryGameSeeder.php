<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryGameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category_games')->truncate();

        $games = DB::table('games')->pluck('id')->toArray();
        $categories = DB::table('categories')->pluck('id')->toArray();

        $data = [];

        foreach ($games as $game) {
            $randomNum = rand(1, 5);
            $rand = array_rand($categories, $randomNum);

            if (is_array($rand)) {
                foreach ($rand as $value) {
                    $data[] = [
                        'category_id' => $categories[$value],
                        'game_id' => $game,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            } else {
                $data[] = [
                    'category_id' => $rand,
                    'game_id' => $game,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('category_games')->insert($data);
    }
}
