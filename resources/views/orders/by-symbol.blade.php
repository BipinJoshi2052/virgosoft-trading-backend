@extends('layouts.admin')

@section('title')
    Orders By Symbol
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Orders</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active"> Orderbook – {{ $symbol }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1" style="background: green !important;"><i class="fas fa-dollar-sign"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Buy Volume</span>
                <span class="info-box-number">
                  {{ $totalBuyVolume }}
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1" style="background: red !important;"><i class="fas fa-dollar-sign"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Sell Volume</span>
                <span class="info-box-number">
                  {{ $totalSellVolume }}
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header" style="background: green;">
                <h3 class="card-title">Buy Orders</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
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
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header" style="background: red;">
                <h3 class="card-title">Sell Orders</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                        <th class="p-2 text-left">Price</th>
                        <th class="p-2 text-left">Amount</th>
                        <th class="p-2 text-left">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($sellOrders as $order)
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
                                No sell orders
                            </td>
                        </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div><!-- /.container-fluid -->
    </section>

@endsection