<?php

namespace App\Http\Controllers;

use App\Constants\TagConst;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends BaseController
{
    public function __construct(TagService $tagService)
    {
        $this->service = $tagService;
    }

    private $baseFilter = [
        'status' => TagConst::ACTIVE
    ];

    public function index(Request $request)
    {
        list($filter, $sort, $limit) = $this->getParamsFromRequest($request);

        $filter = array_merge($filter, $this->baseFilter);

        return $this->service->index($filter, $sort, $limit, ['id', 'name', 'slug']);
    }

    public function list(Request $request)
    {
        list($filter, $sort, $limit, $perPage) = $this->getParamsFromRequest($request);

        $filter = array_merge($filter, $this->baseFilter);

        return $this->service->list($filter, $sort, $perPage);
    }

    public function detail(string $slug)
    {
        $tag = $this->service->detail($slug);

        if (empty($tag)) {
            return response()->json(['message' => "Not found"], 404);
        }
        return $tag;
    }
}
