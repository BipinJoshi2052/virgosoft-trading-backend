<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Asset;
use App\Models\Trade;
use Exception;
use App\Events\OrderMatched;

class MatchingService
{
    const COMMISSION_RATE = 0.015;

    public function getMatchingOrders()
    {
        $buyOrders = Order::where('side', 'buy')
            ->where('status', 1)
            ->orderBy('price', 'desc')
            ->get();

        $sellOrders = Order::where('side', 'sell')
            ->where('status', 1)
            ->orderBy('price', 'asc')
            ->get();

        $matches = [];

        foreach ($buyOrders as $buy) {
            foreach ($sellOrders as $sell) {
                if (
                    $buy->symbol === $sell->symbol &&
                    $sell->price <= $buy->price &&
                    $buy->amount == $sell->amount
                ) {
                    $matches[] = [
                        'symbol' => $buy->symbol,
                        'buy_order' => $buy,
                        'sell_order' => $sell,
                        'buy_order_id' => $buy->id,
                        'buy_order_price' => $buy->price,
                        'sell_order_id' => $sell->id,
                        'sell_order_price' => $sell->price,
                        'execution_price' => $sell->price, // maker price assumption
                        'amount' => $buy->amount,
                        'usd_volume' => $sell->price * $buy->amount,
                    ];
                }
            }
        }

        return $matches;
    }

    public function executeTrade(Order $buyOrder, Order $sellOrder): void
    {
        DB::transaction(function () use ($buyOrder, $sellOrder) {

            // Lock orders
            $buyOrder = Order::where('id', $buyOrder->id)->lockForUpdate()->first();
            $sellOrder = Order::where('id', $sellOrder->id)->lockForUpdate()->first();

            // Validations
            if (
                $buyOrder->status !== Order::STATUS_OPEN ||
                $sellOrder->status !== Order::STATUS_OPEN
            ) {
                throw new \Exception('Orders are no longer open');
            }

            if ($sellOrder->price > $buyOrder->price) {
                throw new \Exception('Price mismatch');
            }

            $executionPrice = $sellOrder->price;
            $amount = $buyOrder->amount;
            $usdVolume = $executionPrice * $amount;
            $commissionPercent = Trade::commissionRate();
            $commission = ($usdVolume * $commissionPercent) / 100;

            // Lock users
            $buyer = User::where('id', $buyOrder->user_id)->lockForUpdate()->first();
            $seller = User::where('id', $sellOrder->user_id)->lockForUpdate()->first();

            // Transfer USD to seller (buyer already locked full USD earlier)
            $seller->increment('balance', $usdVolume - $commission);

            // Transfer asset to buyer
            $buyerAsset = Asset::firstOrCreate(
                ['user_id' => $buyer->id, 'symbol' => $buyOrder->symbol],
                ['amount' => 0, 'locked_amount' => 0]
            );

            $buyerAsset->increment('amount', $amount);

            // Release seller locked asset
            $sellerAsset = Asset::where('user_id', $seller->id)
                ->where('symbol', $sellOrder->symbol)
                ->lockForUpdate()
                ->first();

            $sellerAsset->decrement('locked_amount', $amount);

            // Mark orders filled
            $buyOrder->update(['status' => Order::STATUS_FILLED]);
            $sellOrder->update(['status' => Order::STATUS_FILLED]);

            // Store trade
            Trade::create([
                'buy_order_id' => $buyOrder->id,
                'sell_order_id' => $sellOrder->id,
                'symbol' => $buyOrder->symbol,
                'price' => $executionPrice,
                'amount' => $amount,
                'usd_volume' => $usdVolume,
                'fee' => $commission,
            ]);

            // After successful updates, refresh models
            $buyOrder->refresh();
            $sellOrder->refresh();
            $buyer = $buyOrder->user->refresh();
            $seller = $sellOrder->user->refresh();

            // Prepare updated data payload
            $updatedData = [
                'buyer' => [
                    'balance' => $buyer->balance,
                    'assets' => $buyer->assets()->get(),  // Or specific to symbol
                    'orders' => $buyer->orders()->latest()->limit(10)->get(),  // Limit to avoid large payloads
                ],
                'seller' => [
                    'balance' => $seller->balance,
                    'assets' => $seller->assets()->get(),
                    'orders' => $seller->orders()->latest()->limit(10)->get(),
                ],
            ];

            // Broadcast the event
            event(new OrderMatched($buyOrder, $sellOrder, $updatedData));
        });
    }
}
