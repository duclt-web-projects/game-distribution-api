<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;

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
}
