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
}
