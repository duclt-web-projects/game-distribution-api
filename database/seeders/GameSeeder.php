<?php

namespace Database\Seeders;

use App\Constants\GameConst;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('games')->truncate();

        $games = file_get_contents(base_path('database/files/games.json'));
        $games = json_decode($games);

        $data = [];

        foreach ($games as $key => $game) {
            $data[] = [
                'id' => $key + 1,
                'name' => $game->name,
                'slug' => Str::slug($game->name) . '-' . ($key + 1),
                'status' => GameConst::ACCEPTED,
                'width' => $game->width,
                'height' => $game->height,
                'source_link' => 'games/' . $game->file_game . '/index.html',
                'description' => $game->description,
                'thumbnail' => pare_url_file($game->avatar),
                'video' => 'video/' . $game->video,
                'published_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        DB::table('games')->insert($data);
    }
}
