<?php

namespace App\Services;

use App\Constants\TagConst;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagService extends BaseService
{
    public function __construct()
    {
        $this->model = new Tag();
    }

    public function index(array $filter = [], array $sort = [], int $limit = 0, array $columns = ['*'])
    {
        $this->setRelationsCount(['games']);
        return $this->getAll($filter, $sort, $limit, 0, $columns);
    }

    public function list(array $filter = [], array $sort = [], int $perPage = 0 ,array $columns = ['*'])
    {
        $this->setRelationsCount(['games']);
        return $this->getAll($filter, $sort, 0, $perPage, $columns);
    }

    public function detail(string $slug)
    {
        $category = $this->model->select(['name', 'slug'])
            ->where('slug', $slug)
            ->withCount('games')
            ->first();

        if (!$category) return null;

        return $category;
    }

    public function show(string $id)
    {
        $category = $this->model->find($id);

        if (!$category) return null;

        return $category;
    }

    public function store(array $data)
    {
        $slug = Str::slug($data['name']);

        $isExist = $this->model->where('slug', $slug)->count();

        if ($isExist) {
            return response()->json(['message' => "Tag is existed"], 400);
        }

        $dataInsert = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $tag = $this->model->create($dataInsert);

        return $tag;
    }

    public function edit(string $id, array $data)
    {
        $tag = $this->model->find($id);

        if (!$tag) {
            return response()->json(['message' => "Not found"], 404);
        }

        $data['slug'] = Str::slug($data['name']);
        $data['updated_at'] = now();

        $tag->fill($data)->save();

        return $tag;
    }

    public function changeStatus(string $id)
    {
        $tag = $this->model->find($id);

        if (!$tag) {
            return response()->json(['message' => "Not found"], 404);
        }

        $tag->fill(['status' => $tag->status === TagConst::ACTIVE ? TagConst::INACTIVE : TagConst::ACTIVE])->save();

        return $tag;
    }
}
