@extends('layouts.admin')

@section('title')
    Orders
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
              <li class="breadcrumb-item active">Orders</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <form class="d-flex" method="GET" action="{{ route('orders.index') }}">
                <select class="form-control" name="symbol">
                    <option value="">Select Symbol</option>
                    <option value="BTC" {{ $symbol === 'BTC' ? 'selected' : '' }}>BTC</option>
                    <option value="ETH" {{ $symbol === 'ETH' ? 'selected' : '' }}>ETH</option>
                </select>

                <select class="form-control" name="side">
                    <option value="">Select Side</option>
                    <option value="buy" {{ $side === 'buy' ? 'selected' : '' }}>Buy</option>
                    <option value="sell" {{ $side === 'sell' ? 'selected' : '' }}>Sell</option>
                </select>

                <select class="form-control" name="status">
                    <option value="">Select Status</option>
                    <option value="1" {{ $status == 1 ? 'selected' : '' }}>Open</option>
                    <option value="2" {{ $status == 2 ? 'selected' : '' }}>Filled</option>
                    <option value="3" {{ $status == 3 ? 'selected' : '' }}>Cancelled</option>
                </select>

                <button type="submit" class="btn btn-primary" name="filter" value="1">
                    Filter
                </button>
            </form>
            <!-- /.info-box -->
          </div>

          <div class="col-md-12 mt-5">
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
                      <th>Symbol</th>
                      <th>Side</th>
                      <th>Price ($)</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                        $count = 1;
                    @endphp
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $count++ }}</td>
                            <td>
                                <a href="{{ route('orders.bySymbol', $order->symbol) }}">
                                    {{ $order->symbol }}
                                </a>
                            </td>
                            <td>{{ ucfirst($order->side) }}</td>
                            <td>{{ $order->price }}</td>
                            <td>{{ $order->amount }}</td>
                            <td>
                                @if ($order->status === 1) Open
                                @elseif ($order->status === 2) Filled
                                @else Cancelled
                                @endif
                            </td>
                            <td>
                                @if($order->status === 1)
                                    <button
                                        class="btn btn-danger cancel-order-btn"
                                        data-id="{{ $order->id }}">
                                        Cancel
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No orders found.</td>
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
    <!-- /.content -->

@endsection
@section('scripts')
    <script>
        $(document).on('click', '.cancel-order-btn', function () {

            let orderId = $(this).data('id');

            Swal.fire({
                title: 'Cancel Order?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it',
                cancelButtonText: 'No'
            }).then((result) => {

                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: '/orders/' + orderId + '/cancel',
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function () {
                        toastr.success('Order cancelled successfully');

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        } else {
                            toastr.error('Unable to cancel order');
                        }
                    }
                });
            });
        });
    </script>
@endsection