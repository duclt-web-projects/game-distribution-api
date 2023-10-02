<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategoryController extends BaseController
{
    public function __construct(CategoryService $categoryService)
    {
        $this->service = $categoryService;
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
        $category = $this->service->detail($slug);

        if (empty($category)) {
            return response()->json(['message' => "Not found"], 404);
        }
        return $category;
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
