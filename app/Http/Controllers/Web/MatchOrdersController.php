<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MatchingService;
use App\Models\Order;

class MatchOrdersController extends Controller
{
    public function index(MatchingService $matchingService)
    {
        $matches = $matchingService->getMatchingOrders();
        // dd($matches);
        return view('matches.index', [
            'matches' => $matchingService->getMatchingOrders(),
            'commissionPercent' => config('trading.commission_percent'),
        ]);
        // return response()->json($matches);
    }

    public function execute(
        Order $buyOrder,
        Order $sellOrder,
        MatchingService $matchingService
    ) {
        $matchingService->executeTrade($buyOrder, $sellOrder);

        return response()->json(['message' => 'Trade executed successfully']);
    }
}
