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
            <h1>Orders By Symbol</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Orders By Symbol</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
            @if (session('error'))
                <script>
                    toastr.error("{{ session('error') }}");
                </script>
            @endif
        </div>
        <div class="row">
            <a href="{{ route('orders.index') }}">← Back to Orders</a>
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Orders List</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Side</th>
                      <th>Price</th>
                      <th>Amount</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                        $count = 0;
                    @endphp
                    @foreach  ($orders as $order)
                        <tr>
                            <td>{{ $count++ }}</td>
                            <td>{{ ucfirst($order->side) }}</td>
                            <td>{{ $order->price }}</td>
                            <td>{{ $order->amount }}</td>
                            <td>
                                @if ($order->status === 1) Open
                                @elseif ($order->status === 2) Filled
                                @else Cancelled
                                @endif
                            </td>
                        </tr>   
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection