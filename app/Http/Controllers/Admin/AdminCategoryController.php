<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\AdminCategoryService;
use Illuminate\Http\Request;

class AdminCategoryController extends BaseController
{
    public function __construct(AdminCategoryService $service)
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
