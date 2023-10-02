<?php

namespace App\Services;

use App\Constants\CategoryConst;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CategoryService extends BaseService
{
    public function __construct()
    {
        $this->model = new Category();
    }

    public function index(): Collection
    {
        return $this->getAll();
    }

    public function list(array $filter)
    {
        return $this->getAll($filter, [], 4);
    }

    public function detail(string $slug)
    {
        $category = $this->findBy([['slug', '=', $slug]]);

        if (!$category) return null;

        return $category;
    }

    public function show(string $id)
    {
        $category = $this->find($id);

        if (!$category) return null;

        return $category;
    }

    public function store(array $data)
    {
        $slug = Str::slug($data['name']);

        $isExist = $this->model->where('slug', $slug)->count();

        if ($isExist) {
            return response()->json(['message' => "Category is existed"], 400);
        }

        $dataInsert = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $category = $this->model->create($dataInsert);

        return $category;
    }

    public function edit(string $id, array $data)
    {
        $category = $this->model->find($id);

        if (!$category) {
            return response()->json(['message' => "Not found"], 404);
        }

        $data['slug'] = Str::slug($data['name']);
        $data['updated_at'] = now();

        $category->fill($data)->save();

        return $category;
    }

    public function changeStatus(string $id)
    {
        $game = $this->model->find($id);

        if (!$game) {
            return response()->json(['message' => "Not found"], 404);
        }

        $game->fill(['status' => $game->status === CategoryConst::ACTIVE ? CategoryConst::INACTIVE : CategoryConst::ACTIVE])->save();

        return $game;
    }
}
