<?php

namespace App\Http\Controllers;

use App\Constants\CategoryConst;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategoryController extends BaseController
{
    private $baseFilter = [
        'status' => CategoryConst::ACTIVE
    ];

    public function __construct(CategoryService $categoryService)
    {
        $this->service = $categoryService;
    }

    public function index(Request $request)
    {
        list($filter, $sort, $limit) = $this->getParamsFromRequest($request);

        $filter = array_merge($filter, $this->baseFilter);

        return $this->service->index($filter, $sort, $limit, ['id', 'name', 'slug', 'icon']);
    }

    public function list(Request $request)
    {
        list($filter, $sort, $limit, $perPage) = $this->getParamsFromRequest($request);

        $filter = array_merge($filter, $this->baseFilter);

        return $this->service->list($filter, $sort, $perPage);
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
