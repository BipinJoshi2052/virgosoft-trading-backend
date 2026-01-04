<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderApiController extends Controller
{
    public function index(Request $request, OrderService $service)
    {
        $symbol = $request->get('symbol');
        $side   = $request->get('side');
        $status = $request->get('status');

        return response()->json(
            $service->getUserOrders(auth()->user(), $symbol, $side, $status)
        );
    }

    public function store(Request $request, OrderService $service)
    {
        $order = $service->createOrder(auth()->user(), $request->all());

        return response()->json($order, 201);
    }

    public function cancel(Order $order, OrderService $service)
    {
        $service->cancelOrder(auth()->user(), $order);

        return response()->json(['message' => 'Order cancelled']);
    }
}
