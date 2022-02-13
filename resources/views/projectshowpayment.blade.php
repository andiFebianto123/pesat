@include('header')

<div class="container pb-5 pt-5">
    @if ($error)
        <div class="alert alert-danger" role="alert">
            <strong>{{$error}}</strong>
        </div>
    @endif
        <div class="row">
            <div class="col-md-8">
                <p class="mb-3">Terima kasih, order donasi telah berhasil dibuat. Berikut ini detail donasi Anda :</p>
            </div>
            <div class="col-12 col-md-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h5>Data Order</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-condensed mb-0">
                            <tr>
                                <td>ID</td>
                                <td><b>#{{$order->order_project_id}}</b></td>
                            </tr>
                            <tr>
                                <td>Nama Proyek</td>
                                <td><b>{{$order->project->title ?? '-'}}</b></td>
                            </tr>
                            <tr>
                                <td>Total Donasi</td>
                                <td><b>Rp{{ number_format($order->price, 2, ',', '.') }}</b></td>
                            </tr>
                            <tr>
                                <td>Status Pembayaran</td>
                                <td><b>
                                @if ($order->payment_status == 1)
                                            Menunggu Pembayaran
                                        @elseif ($order->payment_status == 2)
                                            Sudah Dibayar
                                        @else
                                            Batal
                                        @endif
                                    </b></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td><b>{{ $order->created_at->format('d M Y') }}</b></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <p class="mb-0 mt-3">Segera klik button "Bayar Sekarang" untuk melanjutkan pembayaran.</p>
                <p>Jika halaman ini tertutup maka Anda dapat melanjutkan pembayaran pada menu Akun Saya.</p>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h5>Pembayaran</h5>
                    </div>
                    <div class="card-body">
                  @if(!$error)
                    @if ($order->payment_status == 1)
                            <button class="btn btn-primary" id="pay-button">Bayar Sekarang</button>
                        @endif
                  @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        const payButton = document.querySelector('#pay-button');
        payButton.addEventListener('click', function(e) {
            e.preventDefault();
            snap.pay('{{ $snapToken }}', {
                // Optional
                onSuccess: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    console.log(result)
                },
                // Optional
                onPending: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    console.log(result)
                },
                // Optional
                onError: function(result) {
                    /* You may add your own js here, this is just example */
                    // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    console.log(result)
                }
            });
        });
    </script>

@include('footer')