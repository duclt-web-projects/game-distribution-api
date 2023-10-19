<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\AdminGameService;
use App\Services\GameService;
use Illuminate\Http\Request;

class AdminGameController extends BaseController
{
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function list(Request $request)
    {
        list($filter, $sort) = $this->getParamsFromRequest($request);

        return $this->service->gameService($filter, $sort, 0, 10);
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

        return $this->gameService->changeStatus($id, $status);
    }
}
