<?php

namespace App\Http\Controllers\User;

use App\Constants\GameConst;
use App\Http\Controllers\BaseController;
use App\Services\GameService;
use Illuminate\Http\Request;

class UserGameController extends BaseController
{
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function list(Request $request)
    {
        list($filter, $sort) = $this->getParamsFromRequest($request);
        $filter['author_id'] =  auth()->user()->id ?? 0;

        $this->gameService->setRelations(['categories:name,slug', 'tags:name,slug']);

        return $this->gameService->getAll($filter, $sort, 0, 10);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];

        $errors = $this->validate($request, $rules);

        if ($errors) {
            return $this->handleError($errors);
        }

        return $this->gameService->store($request->all());
    }

    public function edit( Request $request, string $id)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'category' => 'sometimes|array',
            'tag' => 'sometimes|array',
            'description' => 'sometimes|string'
        ];

        $errors = $this->validate($request, $rules);

        if ($errors) {
            return $this->handleError($errors);
        }

        return $this->gameService->edit($id, $request->all());
    }

    public function changeStatus(string $id, Request $request)
    {
        $rules = [
            'status' => 'required',
        ];
        $errors = $this->validate($request, $rules);

        if ($errors) {
            return $this->handleError($errors);
        }

        $status = $request->get('status');

        if (!in_array($status, [GameConst::ACCEPTED, GameConst::REJECTED])) {
            return response()->json(['message' => "Status is invalid"], 400);
        }

        return $this->gameService->change($id, 'active', $status);
    }

    public function uploadThumbnail(string $id)
    {
        return $this->gameService->uploadThumbnail($id);
    }

    public function uploadGame(string $id)
    {
        return $this->gameService->uploadGame($id);
    }
}
