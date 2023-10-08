<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Game;

class AdminGameService extends BaseService
{
    private $gameModel;

    public function __construct()
    {
        $this->gameModel = new Game();
    }


    public function list(array $filter)
    {
        $query = $this->gameModel->with(['categories', 'author']);

        if ($filter['name']) {
            $query = $query->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        return  $query->paginate(4);
    }

    public function changeStatus(string $id, string $status)
    {
        $game = $this->gameModel->find($id);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $game->fill(['status' => $status])->save();

        return $game;
    }
}
