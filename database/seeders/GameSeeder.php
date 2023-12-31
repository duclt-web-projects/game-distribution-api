<?php

namespace Database\Seeders;

use App\Constants\GameConst;
use Carbon\Carbon;
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
        $endData = Carbon::now();
        $startDate = Carbon::now()->subDays(3);

        foreach ($games as $key => $game) {
            $data[] = [
                'id' => $key + 1,
                'name' => $game->name,
                'name_vi' => $game->name,
                'slug' => Str::slug($game->name) . '-' . ($key + 1),
                'status' => GameConst::ACCEPTED,
                'active' => GameConst::ACTIVE,
                'width' => $game->width,
                'height' => $game->height,
                'play_times' => rand(10, 1000),
                'is_hot' => $game->is_hot,
                'is_banner' => $game->is_banner,
                'type' => 'javascript',
                'sub_type' => 'webgl',
                'source_link' => $game->source_link,
                'description' => $game->description,
                'description_vi' => $game->description,
                'instruction' => $game->description,
                'instruction_vi' => $game->description,
                'thumbnail' => $game->thumbnail,
                'video' => $game->video ? 'video/' . $game->video : null,
                'published_at' => $this->randomDate($startDate, $endData),
                'created_at' => $this->randomDate($startDate, $endData),
                'updated_at' => $this->randomDate($startDate, $endData),
            ];
        }
        DB::table('games')->insert($data);
    }

    function randomDate($startDate, $endDate)
    {
        // Convert to timetamps
        $min = strtotime($startDate);
        $max = strtotime($endDate);

        // Generate random number using above bounds
        $val = rand($min, $max);

        // Convert back to desired date format
        return date('Y-m-d H:i:s', $val);
    }
}
