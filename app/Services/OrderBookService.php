<?php

namespace App\Services;

use App\Models\Order;

class OrderBookService
{
    public function getOrderBook(string $symbol): array
    {
        $buyOrders = Order::where('symbol', $symbol)
            ->where('side', 'buy')
            ->where('status', Order::STATUS_OPEN)
            ->orderBy('price', 'desc')
            ->get([
                'price',
                'amount',
                'created_at'
            ]);

        $sellOrders = Order::where('symbol', $symbol)
            ->where('side', 'sell')
            ->where('status', Order::STATUS_OPEN)
            ->orderBy('price', 'asc')
            ->get([
                'price',
                'amount',
                'created_at'
            ]);

        return [
            'buy' => $buyOrders,
            'sell' => $sellOrders,
        ];
    }
}
