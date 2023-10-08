<?php

namespace App\Services;

use App\Constants\GameConst;
use App\Models\Game;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class GameService extends BaseService
{
    public function __construct()
    {
        $this->model = new Game();
    }

    public function index(): Collection
    {
        return $this->getAll();
    }

    public function list($filter)
    {
        $query = $this->model;

        if ($filter['name']) {
            $query = $query->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        if ($filter['categories']) {
            $query = $query->whereHas('categories', function ($q) use ($filter) {
                $q->whereIn('categories.id', $filter['categories']);
            });
        }

        return  $query->paginate(4);
    }

    public function detail($id)
    {
        $game = $this->model->with(['categories:name,slug', 'tags:name,slug'])->find($id);
        if (!$game) return null;

        return $game;
    }

    public function promoFeature(): array
    {
        $games = $this->model->withoutGlobalScopes()->limit(4)->get();
        $hotGame = $games[0];
        $featureGame = $games->slice(1);
        return [
            'hotGame' => $hotGame,
            'featureGame' => $featureGame,
        ];
    }

    public function promoList(): Collection
    {
        return $this->model->limit(6)->get();
    }

    public function listByUser(string $userId)
    {
        return $this->model->where('author_id', $userId)->paginate(self::LIMIT);
    }

    public function store(array $data)
    {
        $gameData = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'author_id' => auth()->user()->id ?? 1,
            'description' => $data['description'] ?? '',
            'width' => $data['width'],
            'height' => $data['height'],
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $fileUpload = upload_image('thumbnail');

        if (isset($fileUpload['name'])) {
            $gameData['thumbnail'] = pare_url_file($fileUpload['name']);
        }

        $gameFile = $data['gameFile'];
        $gameFileNameToken = explode('.', $gameFile['name']);
        $path = 'games/';
        $location = $path . $gameFile['name'];

        if (!File::exists($path)) {
            mkdir($path, 0777, true);
        }

        if (move_uploaded_file($gameFile['tmp_name'], $location)) {
            $zip = new ZipArchive();

            if ($zip->open($location)) {
                $zip->extractTo($path);
                $zip->close();
            }
        }

        $gameData['source_link'] = $path . $gameFileNameToken[0] . '/index.html';

        $game = $this->model->create($gameData);
        $game->update(['slug' => Str::slug($data['name'] . ' ' . $game->id)]);

        $categories = json_decode($data['category'], 1);

        $gameCategories = [];

        foreach ($categories as $category) {
            $gameCategories[] = [
                'game_id' => $game->id,
                'category_id' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('category_games')->insert($gameCategories);

        return $game->load('categories', 'tags');
    }

    function Zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } else if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }

    public function edit(string $id, array $data)
    {
        $game = $this->model->find($id);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $data['updated_at'] = now();
        unset($data['category']);

        $game->fill($data)->save();

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
}
