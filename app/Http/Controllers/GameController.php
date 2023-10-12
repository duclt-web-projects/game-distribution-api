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
        $filter = [
            'name' => $request->get('name') ?? '',
            'categories' => $request->get('categories') ? explode(',', $request->get('categories')) : []
        ];
        return $this->service->list($filter);
    }

    public function featuredList(Request $request)
    {
        $order = [
            'type' => $request->get('type'),
        ];
        return $this->service->featuredList($order);
    }

    public function detail(string $slug)
    {
        $token = explode('-', $slug);
        $id = last($token);

        return $this->show($id);
    }

    public function show(string $id)
    {
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
            'category' => 'required',
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

    public function changeStatus(string $id)
    {
        return $this->service->changeStatus($id);
    }
}
