<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait RequestDataTrait
{
    public function getParamsFromRequest(Request $request)
    {
        $sort = $this->getSortFromRequest($request);
        $limit = $this->getLimitFromRequest($request);
        $perPage = $this->getPaginateFromRequest($request);
        $filter = $request->except($this->filterExcept);

        return [$filter, $sort, $limit, $perPage];
    }

    public function getSortFromRequest($request)
    {
        $sort = $request->get('sort');

        if (strpos($sort, '-') === 0) {
            $order = 'desc';
            $sort = substr($sort, 1);
        } else {
            $order = 'asc';
        }

        return $sort ? [$sort, $order] : [];
    }

    public function getLimitFromRequest($request)
    {
        return $request->get('limit') ?? $this->limit;
    }

    public function getPaginateFromRequest($request)
    {
        return $request->get('perPage') ?? $this->perPage;
    }
}
