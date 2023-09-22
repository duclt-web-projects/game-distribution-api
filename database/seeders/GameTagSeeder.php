<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('game_tags')->truncate();

        $games = DB::table('games')->pluck('id')->toArray();
        $tags = DB::table('tags')->pluck('id')->toArray();

        $data = [];

        foreach ($games as $game) {
            $randomNum = rand(2, 5);
            $rand = array_rand($tags, $randomNum);

            foreach ($rand as $value) {
                $data[] = [
                    'game_id' => $game,
                    'tag_id' => $tags[$value],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('game_tags')->insert($data);
    }
}
