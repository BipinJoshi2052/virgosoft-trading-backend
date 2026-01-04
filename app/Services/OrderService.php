<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Asset;

class OrderService
{
    /**
     * Get user orders with optional filters
     */
    public function getUserOrders(
        User $user,
        ?string $symbol = null,
        ?string $side = null,
        ?int $status = null
    ) {
        return Order::where('user_id', $user->id)
            ->when($symbol, fn (Builder $q) =>
                $q->where('symbol', $symbol)
            )
            ->when($side, fn (Builder $q) =>
                $q->where('side', strtolower($side))
            )
            ->when($status, fn (Builder $q) =>
                $q->where('status', $status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Orders by symbol
     */
    public function getOrdersBySymbol(string $symbol)
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

        $totalBuyVolume = $buyOrders->sum(function ($order) {
            return $order->price * $order->amount;
        });

        $totalSellVolume = $sellOrders->sum(function ($order) {
            return $order->price * $order->amount;
        });

        return [
            'buy' => $buyOrders,
            'sell' => $sellOrders,
            'total_buy_volume' => $totalBuyVolume,
            'total_sell_volume' => $totalSellVolume,
        ];
        
        // return Order::where('user_id', $user->id)
        //     ->where('symbol', $symbol)
        //     ->latest()
        //     ->paginate(10);
    }
    
    public function createOrder($user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {

            if ($data['side'] === 'buy') {
                $requiredUsd = $data['price'] * $data['amount'];

                if ($user->balance < $requiredUsd) {
                    throw new \Exception('Insufficient USD balance');
                }

                // Lock USD
                $user->decrement('balance', $requiredUsd);
            }

            if ($data['side'] === 'sell') {
                $asset = Asset::where('user_id', $user->id)
                    ->where('symbol', $data['symbol'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($asset->amount < $data['amount']) {
                    throw new \Exception('Insufficient asset balance');
                }

                $asset->decrement('amount', $data['amount']);
                $asset->increment('locked_amount', $data['amount']);
            }

            return Order::create([
                'user_id' => $user->id,
                'symbol'  => $data['symbol'],
                'side'    => $data['side'],
                'price'   => $data['price'],
                'amount'  => $data['amount'],
                'status'  => 1, // open
            ]);
        });
    }

    public function cancelOrder($user, Order $order): void
    {
        DB::transaction(function () use ($user, $order) {

            // Lock order row
            $order = Order::where('id', $order->id)
                ->lockForUpdate()
                ->first();

            // Validation 1: Ownership
            if ($order->user_id !== $user->id) {
                throw new \Exception('Unauthorized order cancellation');
            }

            // Validation 2: Only OPEN orders
            if ($order->status !== Order::STATUS_OPEN) {
                throw new \Exception('Only open orders can be cancelled');
            }

            // Release locked funds/assets
            if ($order->side === 'buy') {
                $refund = $order->price * $order->amount;
                $user->increment('balance', $refund);
            }

            if ($order->side === 'sell') {
                $asset = Asset::where('user_id', $user->id)
                    ->where('symbol', $order->symbol)
                    ->lockForUpdate()
                    ->first();

                $asset->decrement('locked_amount', $order->amount);
                $asset->increment('amount', $order->amount);
            }

            // Update order status
            $order->update([
                'status' => Order::STATUS_CANCELLED
            ]);
        });
    }
}
