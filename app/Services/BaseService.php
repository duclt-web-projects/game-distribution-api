<?php

namespace App\Services;


use App\Traits\ScopeCondition;
use App\Traits\ScopeRepositoryTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

abstract class BaseService
{
    use ScopeRepositoryTrait;
    use ScopeCondition;
    protected $model;

    protected $relations = [];
    protected $relationsCount = [];

    protected $total = 0;
    protected const LIMIT = 10;

    public function setRelations($relations)
    {
        $this->relations = $relations;
    }

    public function getRelations()
    {
        return $this->relations;
    }

    public function setRelationsCount($relations)
    {
        $this->relationsCount = $relations;
    }

    public function getAll(array $filter = [], array $sort = [], int $limit = 0, int $perPage = 0 ,array $columns = [])
    {
        $query = $this->model->filters($filter);

        if (count($this->relations)) {
            $query = $query->with($this->relations);
        }

        if (count($columns)) {
            $query = $query->select($columns);
        }

        if (count($this->relationsCount)) {
            $query = $query->withCount($this->relationsCount);
        }

        if (count($sort)) {
            list($col, $dir) = $sort;
            $query = $query->withoutGlobalScopes()->orderBy($col, $dir);
        }

        if ($limit) {
            return $query->limit($limit)->get();
        }

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findById($id, array $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    public function findBy(array $condition = [], array $columns = ['*'])
    {
        $query = $this->model;
        $item = $this->scopeFilter($query, $condition)->first($columns);
        return $item;
    }

    public function updateOrCreateData($id, array $data = [])
    {
        return $this->model->updateOrCreate([$this->model->getPrimaryKey() => $id], $data);
    }

    public function updateById($id, array $data = [])
    {
        $model = $this->findById($id);
        $model->fill($data)->save();
        return $model;
    }

    public function updateByField($id, $field, string $otherValue = '')
    {
        $row = $this->findById($id);
        $row->$field = ($otherValue ?: (($row->$field == 1) ? 0 : 1));
        $row->save();
        return $row;
    }

    public function delete($id)
    {
        return is_array($id) ? $this->model->destroy($id) : $this->findById($id)->delete();
    }

    public function getPluck($value, $key)
    {
        return $this->model->pluck($value, $key);
    }

    public function getInstance()
    {
        return new $this->model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function insert(array $data = []): bool
    {
        return DB::table($this->model->getTable())->insert($data);
    }

    public function insertGetId(array $data = []): int
    {
        return DB::table($this->model->getTable())->insertGetId($data);
    }

    public function update($id, array $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function updateBy($column, $value, array $data)
    {
        return $this->model->where($column, $value)->update($data);
    }

    public function countBy(array $filter)
    {
        $query = $this->model->whereRaw(1);
        $query = $this->scopeFilter($query, $filter);

        return $query->count();
    }

    public function increment($id, $column, int $hit = 1)
    {
        return $this->model->where('id', $id)->increment($column, $hit);
    }

    public function updateOrCreate($filter, $data)
    {
        return $this->model->updateOrCreate($filter, $data);
    }

    public function valueById($id, $column)
    {
        return $this->model->where('id', $id)->value($column);
    }

    public function firstById($id, array $columns = ['*'])
    {
        return $this->model->where('id', $id)->first($columns);
    }

    public function first(array $filter = [], array $columns = ['*'])
    {
        $result = $this->model;
        if ($relation = Arr::get($filter, 'relation')) {
            $result = $this->scopeRelation($result, $relation);
        }
        if ($where = Arr::get($filter, 'where')) {
            $result = $this->scopeWhere($result, $where);
        }
        if ($order = Arr::get($filter, 'order')) {
            $result = $this->scopeOrder($result, $order);
        }

        return $result->first($columns);
    }

    public function findOneById($id, array $filter = [], array $columns = ['*'])
    {
        $result = $this->model;
        if (!empty($filter)) {
            $relation = Arr::get($filter, 'relation');
            $where = Arr::get($filter, 'where');

            if ($relation) {
                $result = $this->scopeRelation($result, $relation);
            }
            if ($where) {
                $result = $this->scopeWhere($result, $where);
            }
        }

        return $result->find($id, $columns);
    }

    public function getAllRecord(array $filter = [], array $columns = ['*'], int $paginate = 0)
    {
        $result = $this->model;
        if (!empty($filter)) {
            $relation = $filter['relation'] ?? null;
            $join = $filter['join'] ?? null;
            $whereNotIn = $filter['whereNotIn'] ?? null;
            $whereIn = $filter['whereIn'] ?? null;
            $where = $filter['where'] ?? null;
            $orWhere = $filter['orWhere'] ?? null;
            $order = $filter['order'] ?? null;
            $limit = $filter['limit'] ?? null;
            $between = $filter['between'] ?? null;
            $whereDate = $filter['whereDate'] ?? null;
            $whereMonth = $filter['whereMonth'] ?? null;
            if ($relation) {
                $result = $this->scopeRelation($result, $relation);
            }
            if ($join) {
                $result = $this->scopeJoin($result, $join);
            }
            if ($whereIn) {
                $result = $this->scopeWhereIn($result, $whereIn);
            }
            if ($whereNotIn) {
                $result = $this->scopeWhereNotIn($result, $whereNotIn);
            }
            if ($where) {
                $result = $this->scopeWhere($result, $where);
            }
            if ($orWhere) {
                $result = $this->scopeOrWhere($result, $orWhere);
            }
            if ($order) {
                $result = $this->scopeOrder($result, $order);
            }
            if ($between) {
                $result = $this->scopeBetween($result, $between);
            }
            if ($whereDate) {
                $result = $this->scopeWhereDate($result, $whereDate);
            }
            if ($whereMonth) {
                $result = $this->scopeWhereMonth($result, $whereMonth);
            }
            if ($limit) {
                $result = $result->limit($limit);
            }
        }

        return $paginate ? $result->paginate($paginate, $columns) : $result->get($columns);
    }
}
