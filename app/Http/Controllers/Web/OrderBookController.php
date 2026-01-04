<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderBookService;

class OrderBookController extends Controller
{
    public function show(string $symbol, OrderBookService $orderBookService)
    {
        $symbol = strtoupper($symbol);

        if (!in_array($symbol, ['BTC', 'ETH'])) {
            abort(404);
        }

        $orderBook = $orderBookService->getOrderBook($symbol);

        return view('orderbook.show', [
            'symbol' => $symbol,
            'buyOrders' => $orderBook['buy'],
            'sellOrders' => $orderBook['sell'],
        ]);
    }
}
