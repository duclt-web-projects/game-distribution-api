<?php

namespace App\Services;

use App\Constants\CategoryConst;
use App\Constants\GameConst;
use App\Models\Game;
use App\Models\GameRating;
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

    public function index($filter, $sort, $limit)
    {
        $query = $this->model->withAvg('comments as rating', 'rating')->filters($filter);

        if (count($sort)) {
            list($col, $dir) = $sort;
            $query = $query->withoutGlobalScopes()->orderBy($col, $dir);
        }

        if ($limit) {
            return $query->limit($limit)->get();
        }

        return $query->get();
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
        $game = $this->model->with([
            'categories' => function ($query) {
                return $query->select(['categories.id', 'name', 'slug'])->where('category_games.status', 1);
            },
            'tags' => function ($query) {
                return $query->select(['tags.id', 'name', 'slug'])->where('game_tags.status', 1);
            }])->find($id);

        if (!$game) return null;

        return $game;
    }

    public function store(array $data)
    {
        $gameData = [
            'name' => $data['name'],
            'author_id' => auth()->user()->id ?? 1,
            'status' => GameConst::ACCEPTED,
            'published_at' => now(),
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

        if (array_key_exists('categories', $data)) {
            $categories = $data['categories'];
            $categoryGames = DB::table('category_games')
                ->where('game_id', $id)
                ->get()
                ->pluck('category_id', 'id')
                ->toArray();

            list($newCategories, $activeGameCategories, $inactiveGameCategories) = $this->convertData($categoryGames, $categories);

            $newGameCategories = [];
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

        if (array_key_exists('tags', $data)) {
            $tags = $data['tags'];
            $tagGames = DB::table('game_tags')->where('game_id', $id)
                ->get()
                ->pluck('tag_id', 'id')
                ->toArray();

            list($newTags, $activeGameTags, $inactiveGameTags) = $this->convertData($tagGames, $tags);

            $newGameTags = [];
            foreach ($newTags as $tag) {
                $newGameTags[] = [
                    'game_id' => $game->id,
                    'tag_id' => $tag,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            unset($data['tags']);
        }

        $game->fill($data)->save();

        if (isset($newGameCategories) && count($newGameCategories)) {
            DB::table('category_games')->insert($newGameCategories);
        }

        if (isset($inactiveGameCategories) && count($inactiveGameCategories)) {
            DB::table('category_games')->whereIn('id', array_keys($inactiveGameCategories))
                ->update(['status' => 0, 'updated_at' => now()]);
        }

        if (isset($activeGameCategories) && count($activeGameCategories)) {
            DB::table('category_games')->whereIn('id', array_keys($activeGameCategories))
                ->update(['status' => 1, 'updated_at' => now()]);
        }

        if (isset($newGameTags) && count($newGameTags)) {
            DB::table('game_tags')->insert($newGameTags);
        }

        if (isset($inactiveGameTags) && count($inactiveGameTags)) {
            DB::table('game_tags')->whereIn('id', array_keys($inactiveGameTags))
                ->update(['status' => 0, 'updated_at' => now()]);
        }

        if (isset($activeGameTags) && count($activeGameTags)) {
            DB::table('game_tags')->whereIn('id', array_keys($activeGameTags))
                ->update(['status' => 1, 'updated_at' => now()]);
        }

        return $game;
    }

    private function convertData($dataDb, $dataRequest)
    {
        $newRecords = array_diff($dataRequest, $dataDb);
        $inactiveRecords = array_diff($dataDb, $dataRequest);
        $activeRecords = array_intersect($dataDb, $dataRequest);

        return [$newRecords, $activeRecords, $inactiveRecords];
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

    public function listComments(string $gameId)
    {
        $game = $this->model->find($gameId);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        return GameRating::query()->with('user:id,name,avatar')->where('game_id', $game->id)
            ->where('status', 1)
            ->get();
    }

    public function addComment(string $gameId, array $data)
    {
        $game = $this->model->find($gameId);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $data['user_id'] = auth()->user()->id ?? 1;
        $data['game_id'] = $game->id;
        $data['created_at'] = now();

        return GameRating::create($data);
    }

    public function editComment(string $gameId, string $commentId, array $data)
    {
        $game = $this->model->find($gameId);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $comment = GameRating::find($commentId);

        if (!$comment) {
            return response()->json(['message' => "Comment is not found"], 404);
        }

        $comment->fill($data)->save();

        return $comment;
    }

    public function gamesByCategory(string $slug)
    {
        $category = DB::table('categories')->where('slug', $slug)->first();

        if(!$category) {
            return response()->json(['message' => "Category is not found"], 404);
        }

        $games = $this->model->whereHas('categories', function (Builder $query) use ($category){
            $query->where('categories.id', $category->id);
        })->get();

        return $games;
    }

    public function increasePlayTimes(string $id)
    {
        $game = $this->model->find($id);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $game->fill(['play_times' => ++$game->play_times])->save();

        return $game;
    }
}
