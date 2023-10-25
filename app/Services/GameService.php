<?php

namespace App\Services;

use App\Constants\GameConst;
use App\Models\Game;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class GameService extends BaseService
{
    public function __construct()
    {
        $this->model = new Game();
    }

    public function index()
    {
        return $this->getAll([['status', '=', GameConst::ACCEPTED]]);
    }

    public function featuredList($data)
    {
        $query = $this->model->where([
            ['status', '=', GameConst::ACCEPTED],
            ['active', '=', GameConst::ACTIVE]
        ]);

        if ($data['type']) {
            $query = $query->orderBy($data['type'], 'desc');
        }

        return $query->limit($data['limit'])->get();
    }

    public function detail($id)
    {
//        (['categories:id,name,slug', 'tags:id,name,slug'])/
        $game = $this->model->with([
            'categories' => function ($query) {
                return $query->where('category_games.status', 1);
            },
            'tags' => function ($query) {
                return $query->where('game_tags.status', 1);
            }])->find($id);

        if (!$game) return null;

        return $game;
    }

    public function store(array $data)
    {
        $gameData = [
            'name' => $data['name'],
            'author_id' => auth()->user()->id ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $game = $this->model->create($gameData);
        $game->update(['slug' => Str::slug($data['name'] . ' ' . $game->id)]);

        return $game;
    }

    public function edit(string $id, array $data)
    {
        $game = $this->model->find($id);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $data['updated_at'] = now();

        $newGameCategories = [];
        $removeGameCategories = [];

        if (array_key_exists('categories', $data)) {
            $categories = explode(',', $data['categories']);

            $categoryGame = DB::table('category_games')->where('game_id', $id)
                ->get()->pluck('category_id', 'id')->toArray();

            $newCategories = array_diff($categories, $categoryGame);
            $removeGameCategories = array_diff($categoryGame, $categories);

            foreach ($newCategories as $category) {
                $newGameCategories[] = [
                    'game_id' => $game->id,
                    'category_id' => $category,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            unset($data['categories']);
        }

        $game->fill($data)->save();

        if (count($newGameCategories)) {
            DB::table('category_games')->insert($newGameCategories);
        }

        if (count($removeGameCategories)) {
            DB::table('category_games')->whereIn('id', array_keys($removeGameCategories))
                ->update(['status' => 0, 'updated_at' => now()]);
        }

        return $game;
    }

    public function changeStatus(string $id)
    {
        $game = $this->model->find($id);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $game->fill(['status' => $game->status === GameConst::ACTIVE ? GameConst::INACTIVE : GameConst::ACTIVE])->save();

        return $game;
    }

    public function change(string $id, string $col, string $value, $isUser = false)
    {
        $query = $this->model;

        if ($isUser) {
            $query = $query->where('author_id', auth()->user()->id ?? 0);
        }

        $game = $query->where('id', $id)->first();

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $game->fill([$col => $value])->save();

        return $game;
    }

    public function uploadThumbnail(string $id)
    {
        $game = $this->model->find($id);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $fileUpload = upload_image('thumbnail', 'thumbnails');

        if (isset($fileUpload['name'])) {
            $fileName = pare_url_file($fileUpload['name'], 'thumbnails');
            $game->fill(['thumbnail' => $fileName])->save();
        }

        return $game;
    }

    public function uploadGame(string $id)
    {
        $game = $this->model->find($id);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $gameFile = $_FILES['gameFile'];
        $gameFileNameToken = explode('.', $gameFile['name']);
        $path = 'games/' . $gameFileNameToken[0];
        $location = 'games/' . $gameFile['name'];
        $fileDefault = 'index.html';

        if (!File::exists($path)) {
            mkdir($path, 0777, true);
        }

        if (move_uploaded_file($gameFile['tmp_name'], $location)) {
            $zip = new ZipArchive();
            $checkExist = false;

            if ($zip->open($location)) {
                // Loop through the files in the ZIP archive
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);

                    // Check if the filename matches the one you're looking for
                    if ($filename === $fileDefault) {
                        $checkExist = true;
                        break;
                    }
                }

                if ($checkExist) {
                    $zip->extractTo($path);
                    $zip->close();
                } else {
                    return response()->json(['message' => 'No index.html file'], 400);
                }
            } else {
                return response()->json(['message' => 'Failed to extract ZIP file.'], 500);
            }

            $jsFilePath = '/assets/js/game.js';
            $indexFilePath = $path . '/index.html';

            // Read the index.html file
            $htmlContent = file_get_contents($indexFilePath);

            // Generate the CSS link tag
            $scriptTag = '<script src="' . $jsFilePath . '"></script>';

            // Find the closing </head> tag and insert the CSS link before it
            $htmlContent = str_replace('</body>', $scriptTag . '</body>', $htmlContent);

            // Save the modified HTML content back to the index.html file
            file_put_contents($indexFilePath, $htmlContent);
        }

        $game->fill(['source_link' => $indexFilePath])->save();

        return $game;
    }
}
