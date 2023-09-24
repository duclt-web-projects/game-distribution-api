<?php

namespace App\Http\Controllers;

use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GameController extends BaseController
{
    public function __construct(GameService $gamesService)
    {
        $this->service = $gamesService;
    }

    public function index(): Collection
    {
        return $this->service->index();
    }

    public function list(Request $request)
    {
        $this->setFilter($request, 'name', 'LIKE');
        $filter = $this->getFilter();
        return $this->service->list($filter);
    }

    public function detail(string $slug)
    {
        $token = explode('-', $slug);
        $id = last($token);
        $game = $this->service->detail($id);

        if (empty($game)) {
            return response()->json(['message' => "Not found"], 404);
        }
        return $game;
    }

    public function promoFeature(): array
    {
        return $this->service->promoFeature();
    }

    public function promoList(): Collection
    {
        return $this->service->promoList();
    }

    public function listByUser(string $userId)
    {
        return $this->service->listByUser($userId);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'thumbnail' => 'required|file|mimes:jpg,png,jpeg',
            'gameFile' => 'required|file|mimes:zip|max:10240',
        ];

        $errors = $this->validate($request, $rules);

        if ($errors) {
            return $this->handleError($errors);
        }

        $data = $request->except(['thumbnail', 'gameFile']);
        $data['thumbnail'] = $_FILES['thumbnail'];
        $data['gameFile'] = $_FILES['gameFile'];

        return $this->service->store($data);
    }

    public function edit(string $id, Request $request)
    {
        $rules = [
            'name' => 'nullable|string|max:255',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
        ];

        $errors = $this->validate($request, $rules);
        if ($errors) {
            return $this->handleError($errors);
        }

        return $this->service->edit($id, $request->all());
    }

    public function delete(string $id)
    {
        $this->service->delete($id);
    }

}
