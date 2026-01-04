<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->middleware('auth');
        $this->orderService = $orderService;
    }

    /**
     * Orders page with filters
     */
    public function index(Request $request)
    {
        $symbol = $request->get('symbol');
        $side   = $request->get('side');
        $status = $request->get('status');

        $orders = $this->orderService->getUserOrders(
            Auth::user(),
            $symbol,
            $side,
            $status
        );

        return view('orders.index', compact(
            'orders',
            'symbol',
            'side',
            'status'
        ));
    }

    /**
     * Orders by symbol page
     */
    public function bySymbol(string $symbol)
    {
        $symbol = strtoupper($symbol);

        if (!in_array($symbol, ['BTC', 'ETH'])) {
            abort(404);
        }

        $orders = $this->orderService->getOrdersBySymbol($symbol);
        // $orders = $this->orderService
            // ->getOrdersBySymbol(strtoupper($symbol));
            // ->getOrdersBySymbol(Auth::user(), strtoupper($symbol));
        return view('orders.by-symbol', [
            'symbol' => $symbol,
            'buyOrders' => $orders['buy'],
            'sellOrders' => $orders['sell'],    
            'totalBuyVolume' => $orders['total_buy_volume'],
            'totalSellVolume' => $orders['total_sell_volume'],
        ]);
        // return view('orders.by-symbol', compact('orders', 'symbol'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request, OrderService $orderService)
    {
        $validated = $request->validate([
            'symbol' => 'required|in:BTC,ETH',
            'side'   => 'required|in:buy,sell',
            'price'  => 'required|numeric|min:0.01',
            'amount' => 'required|numeric|min:0.00000001',
        ]);

        $orderService->createOrder(auth()->user(), $validated);

        return redirect()->back()->with('success', 'Order placed successfully');
    }

    public function cancel(Order $order, OrderService $orderService)
    {
        $orderService->cancelOrder(auth()->user(), $order);

        return response()->json(['message' => 'Order cancelled']);
    }
}
