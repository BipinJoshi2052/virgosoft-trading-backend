@extends('layouts.admin')

@section('title')
    Limit Order Form
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Limit Order Form</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Limit Order Form</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <form id="orderForm">
                    <div class="row">
                        @csrf

                        <!-- Symbol -->
                        <div class="col-md-3 form-group">
                            <label class="block mb-1">Symbol</label>
                            <select id="symbol" class="form-control">
                                <option value="">Select</option>
                                <option value="BTC">BTC</option>
                                <option value="ETH">ETH</option>
                            </select>
                        </div>

                        <!-- Side -->
                        <div class="col-md-3 form-group">
                            <label class="block mb-1">Side</label>
                            <select id="side" class="form-control">
                                <option value="">Select</option>
                                <option value="buy">Buy</option>
                                <option value="sell">Sell</option>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="col-md-3 form-group">
                            <label class="block mb-1">Price (USD)</label>
                            <input type="number" step="1" id="price" class="form-control">
                        </div>

                        <!-- Amount -->
                        <div class="col-md-3 form-group">
                            <label class="block mb-1">Amount</label>
                            <input type="number" step="0.01" id="amount" class="form-control">
                        </div>
                        <div class="col-md-3 form-group">
                            <button id="submitOrder" class="btn btn-primary">
                                Place Order
                            </button>
                        </div>

                    </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {

            $('#orderForm').on('submit', function (e) {
                e.preventDefault();

                let symbol = $('#symbol').val();
                let side   = $('#side').val();
                let price  = $('#price').val();
                let amount = $('#amount').val();
                let button = $('#submitOrder');

                // Validation
                if (!symbol || !side || !price || !amount) {
                    toastr.error('Please fill all fields before placing an order');
                    return;
                }

                // Confirmation
                Swal.fire({
                    title: 'Confirm Order',
                    text: `Place ${side.toUpperCase()} order for ${amount} ${symbol} @ ${price} USD?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, place order',
                    cancelButtonText: 'Cancel'
                }).then((result) => {

                    if (!result.isConfirmed) {
                        return;
                    }

                    // Disable button
                    button.prop('disabled', true).text('Processing...');

                    $.ajax({
                        url: "{{ route('orders.store') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            symbol: symbol,
                            side: side,
                            price: price,
                            amount: amount
                        },
                        success: function (response) {
                            toastr.success('Order placed successfully');

                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function (xhr) {

                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function (key, value) {
                                    toastr.error(value[0]);
                                });
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                toastr.error(xhr.responseJSON.message);
                            } else {
                                toastr.error('Something went wrong. Please try again.');
                            }
                        },
                        complete: function () {
                            button.prop('disabled', false).text('Place Order');
                        }
                    });
                });
            });

        });
    </script>
@endsection