<?php

namespace App\Http\Controllers;

use App\Constants\GameConst;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GameController extends BaseController
{
    private $baseFilter = [
        'status' => GameConst::ACCEPTED,
        'active' => GameConst::ACTIVE,
    ];

    private $baseCol = ['id', 'name', 'slug', 'thumbnail'];

    public function __construct(GameService $gamesService)
    {
        $this->service = $gamesService;
    }

    public function index()
    {
        return $this->service->index();
    }

    public function list(Request $request)
    {
        list($filter, $sort, $limit, $perPage) = $this->getParamsFromRequest($request);

        $filter = array_merge($filter, $this->baseFilter);

        return $this->service->getAll($filter, $sort, $limit, $perPage);
    }

    public function featuredList(Request $request)
    {
        $order = [
            'type' => $request->get('type'),
            'limit' => $request->get('limit') ?? 7,
        ];
        return $this->service->featuredList($order);
    }

    public function detail(string $slug)
    {
        $token = explode('-', $slug);
        $id = last($token);

        return $this->show($id);
    }

    public function show(string $id)
    {
        $game = $this->service->detail($id);

        if (empty($game)) {
            return response()->json(['message' => "Not found"], 404);
        }
        return $game;
    }

    public function promoFeature(): array
    {
        $games = $this->service->getAll($this->baseFilter, ['is_hot', 'desc'], 4, 0, $this->baseCol);

        return [
            'hotGame' => $games[0],
            'featureGame' => $games->slice(1)
        ];
    }

    public function promoList(): Collection
    {
        return $this->service->getAll($this->baseFilter, ['id', 'asc'], 6, 0, $this->baseCol);
    }
}
