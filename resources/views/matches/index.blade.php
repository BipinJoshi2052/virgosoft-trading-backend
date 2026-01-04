@extends('layouts.admin')

@section('title')
    Matches List
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Matches List</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active"> Matches List</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">

        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header" style="background: green;">
                <h3 class="card-title">Matches List</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Symbol</th>
                        <th>Buy Price</th>
                        <th>Sell Price</th>
                        <th>Amount</th>
                        <th>USD Volume</th>
                        <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>   
                    @php
                        $count = 1;
                    @endphp
                    @forelse($matches as $match)
                        <tr>
                            <td>{{ $count++ }}</td>
                            <td>{{ $match['symbol'] }}</td>
                            <td>{{ $match['buy_order_price'] }}</td>
                            <td>{{ $match['sell_order_price'] }}</td>
                            <td>{{ $match['amount'] }}</td>
                            <td>{{ number_format($match['usd_volume'], 2) }}</td>
                            <td>
                                <button
                                    class="btn btn-success match-btn"
                                    data-buy="{{ $match['buy_order_id'] }}"
                                    data-sell="{{ $match['sell_order_id'] }}"
                                    data-symbol="{{ $match['symbol'] }}"
                                    data-amount="{{ $match['amount'] }}"
                                    data-price="{{ $match['execution_price'] }}"
                                    data-buyer="{{ $match['buy_order']->user->name }}"
                                    data-seller="{{ $match['sell_order']->user->name }}">
                                    Match
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center">
                                No matches found
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

@section('scripts')
<script>
    const COMMISSION_PERCENT = {{ $commissionPercent }};
</script>
    <script>
        $(document).on('click', '.match-btn', function () {

            let buyId = $(this).data('buy');
            let sellId = $(this).data('sell');
            const symbol  = $(this).data('symbol');
            const amount  = parseFloat($(this).data('amount'));
            const price   = parseFloat($(this).data('price'));
            const buyer   = $(this).data('buyer');
            const seller  = $(this).data('seller');

            const usdTotal     = (amount * price).toFixed(2);
            const commission   = ((usdTotal * COMMISSION_PERCENT) / 100).toFixed(2);
            const sellerNet    = (usdTotal - commission).toFixed(2);

            const description = `
            <div style="text-align:left;font-size:14px;">
                <h4><b>Trade Summary</b></h4>
                <hr>

                <p><b>Buyer:</b> ${buyer}</p>
                <p><b>Seller:</b> ${seller}</p>

                <hr>

                <p>
                    <b>${buyer}</b> placed a <b>BUY</b> order for
                    <b>${amount} ${symbol}</b> @ <b>${price} USD</b><br>
                    <b>Total:</b> ${usdTotal} USD
                </p>

                <p>
                    <b>${seller}</b> placed a <b>SELL</b> order for
                    <b>${amount} ${symbol}</b>
                </p>

                <hr>

                <p>📤 <b>${amount} ${symbol}</b> will be deducted from <b>${seller}</b> and transferred to <b>${buyer}</b></p>

                <p>📥 <b>${usdTotal} USD</b> will be deducted from <b>${buyer}</b> and transferred to <b>${seller}</b></p>

                <p>💸 <b>${commission} USD</b> will be deducted from <b>${seller}</b> as trading commission</p>

                <p><b>Seller Net Amount:</b> ${sellerNet} USD</p>

                <hr>

                <p style="color:red;">
                    ⚠️ This action is irreversible. Please confirm to execute the trade.
                </p>
            </div>
        `;

            Swal.fire({
                title: 'Execute Trade?',
                html: description,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, match'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.ajax({
                    url: `/match-orders/${buyId}/${sellId}`,
                    method: 'POST',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function () {
                        toastr.success('Trade executed successfully');
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON?.message ?? 'Trade failed');
                    }
                });
            });
        });
    </script>
@endsection