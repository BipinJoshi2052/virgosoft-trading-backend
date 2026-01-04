<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\MatchingService;


class MatchOrdersApiController extends Controller
{

    protected MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * GET /api/match-orders
     * Returns list of valid matching BUY & SELL orders
     */
    public function index()
    {
        $matches = $this->matchingService->getMatchingOrders();

        return response()->json([
            'success' => true,
            'data' => $matches,
            'commissionPercent' => config('trading.commission_percent'),
        ]);
    }

    /**
     * POST /api/match-orders/{buyOrder}/{sellOrder}
     * Executes the trade
     */
    public function execute(
        Order $buyOrder,
        Order $sellOrder
    ){

        $trade = $this->matchingService->executeTrade(
            $buyOrder,
            $sellOrder
        );

        return response()->json([
            'success' => true,
            'message' => 'Trade executed successfully',
            'data' => $trade,
        ]);
    }
}
