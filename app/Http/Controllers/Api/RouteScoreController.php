<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScoreRoutesRequest;
use App\Http\Resources\Api\RouteScoreResource;
use App\Services\SafetyScoreService;

class RouteScoreController extends Controller
{
    public function __invoke(ScoreRoutesRequest $request, SafetyScoreService $safetyScoreService): RouteScoreResource
    {
        $scores = $safetyScoreService->score($request->validated('routes'));

        return new RouteScoreResource($scores);
    }
}
