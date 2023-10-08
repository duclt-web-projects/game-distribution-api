<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\AdminGameService;
use Illuminate\Http\Request;

class AdminGameController extends BaseController
{
    public function __construct(AdminGameService $service)
    {
        $this->service = $service;
    }

    public function list(Request $request)
    {
        $filter = [
            'name' => $request->get('name') ?? '',
        ];
        return $this->service->list($filter);
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

        return $this->service->changeStatus($id, $status);
    }
}
