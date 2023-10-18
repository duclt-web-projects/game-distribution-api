<?php

namespace App\Services;

use App\Constants\CategoryConst;
use App\Models\Category;
use Illuminate\Support\Str;

class AdminCategoryService extends BaseService
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function list(array $filter)
    {
        $query = $this->categoryModel->withCount('games');

        if ($filter['name']) {
            $query = $query->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        return  $query->paginate(self::LIMIT);
    }

    public function show(string $id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) return null;

        return $category;
    }

    public function store(array $data)
    {
        $slug = Str::slug($data['name']);

        $isExist = $this->categoryModel->where('slug', $slug)->count();

        if ($isExist) {
            return response()->json(['message' => "Category is existed"], 400);
        }

        $dataInsert = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $category = $this->categoryModel->create($dataInsert);

        return $category;
    }

    public function edit(string $id, array $data)
    {
        $category = $this->categoryModel->find($id);

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
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return response()->json(['message' => "Not found"], 404);
        }

        $category->fill(['status' => $category->status === CategoryConst::ACTIVE ? CategoryConst::INACTIVE : CategoryConst::ACTIVE])->save();

        return $category;
    }
}
