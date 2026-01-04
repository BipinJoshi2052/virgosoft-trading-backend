@extends('layouts.admin')

@section('title')
    OrderBook
@endsection

@section('content')
<div class="max-w-6xl mx-auto mt-8">

    <h2 class="text-2xl font-bold mb-6">
        Orderbook – {{ $symbol }}
    </h2>

    <div class="grid grid-cols-2 gap-8">

        <!-- BUY ORDERS -->
        <div>
            <h3 class="text-green-600 font-semibold mb-3">Buy Orders</h3>

            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 text-left">Price</th>
                        <th class="p-2 text-left">Amount</th>
                        <th class="p-2 text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($buyOrders as $order)
                        <tr>
                            <td class="p-2 text-green-600">
                                {{ number_format($order->price, 2) }}
                            </td>
                            <td class="p-2">{{ $order->amount }}</td>
                            <td class="p-2">
                                {{ number_format($order->price * $order->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center">
                                No buy orders
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- SELL ORDERS -->
        <div>
            <h3 class="text-red-600 font-semibold mb-3">Sell Orders</h3>

            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 text-left">Price</th>
                        <th class="p-2 text-left">Amount</th>
                        <th class="p-2 text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sellOrders as $order)
                        <tr>
                            <td class="p-2 text-red-600">
                                {{ number_format($order->price, 2) }}
                            </td>
                            <td class="p-2">{{ $order->amount }}</td>
                            <td class="p-2">
                                {{ number_format($order->price * $order->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center">
                                No sell orders
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
