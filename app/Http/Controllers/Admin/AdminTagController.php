<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\TagService;
use Illuminate\Http\Request;

class AdminTagController extends BaseController
{
    public function __construct(TagService $service)
    {
        $this->service = $service;
    }

    public function list(Request $request)
    {
        list($filter, $sort, $limit, $perPage) = $this->getParamsFromRequest($request);

        return $this->service->list($filter, $sort, $perPage);
    }

    public function show(string $id)
    {
        $category = $this->service->show($id);

        if (empty($category)) {
            return response()->json(['message' => "Not found"], 404);
        }
        return $category;
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

        return $this->service->store($request->all());
    }

    public function edit(string $id, Request $request)
    {
        $rules = [
            'name' => 'nullable|string|max:255',
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
