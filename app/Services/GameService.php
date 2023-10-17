<?php

namespace App\Services;

use App\Constants\GameConst;
use App\Models\Game;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class GameService extends BaseService
{
    public function __construct()
    {
        $this->model = new Game();
    }

    public function index(): Collection
    {
        return $this->getAll([['status', '=', GameConst::ACCEPTED]]);
    }

    public function list($filter)
    {
        $query = $this->model->where('status', GameConst::ACCEPTED);

        if ($filter['name']) {
            $query = $query->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        if ($filter['categories']) {
            $query = $query->whereHas('categories', function ($q) use ($filter) {
                $q->whereIn('categories.id', $filter['categories']);
            });
        }

        return $query->paginate(4);
    }

    public function featuredList($order)
    {
        $query = $this->model->where('status', GameConst::ACCEPTED);

        if ($order['type']) {
            $query = $query->orderBy($order['type'], 'desc');
        }

        return $query->limit(7)->get();
    }

    public function detail($id)
    {
        $game = $this->model->with(['categories:name,slug', 'tags:name,slug'])->find($id);
        if (!$game) return null;

        return $game;
    }

    public function promoFeature(): array
    {
        $games = $this->model->withoutGlobalScopes()->where('status', GameConst::ACCEPTED)->limit(4)->get();
        $hotGame = $games[0];
        $featureGame = $games->slice(1);
        return [
            'hotGame' => $hotGame,
            'featureGame' => $featureGame,
        ];
    }

    public function promoList(): Collection
    {
        return $this->model->where('status', GameConst::ACCEPTED)->limit(6)->get();
    }

    public function listByUser(string $userId)
    {
        return $this->model->where('author_id', $userId)->paginate(self::LIMIT);
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
