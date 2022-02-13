@include('header')

<div class="container pb-5 pt-5">
    <div class="row">
        <div class="col-md-8">
            <p class="mb-3">Berikut ini merupakan data detail donasi Anda :</p>
        </div>
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h5>Data Order</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-condensed mb-0">
                        <tr>
                            <td>Nama Anak</td>
                            <td><b>{{ $child->full_name }}</b></td>
                        </tr>
                        <tr>
                            <td>Total Donasi</td>
                            <td><b>Rp{{ number_format($total, 2, ',', '.') }}</b></td>
                        </tr>
                        <tr>
                            <td>Periode Donasi</td>
                            <td><b>
                                  Per {{$period}} Bulan
                                </b></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td><b>{{Carbon\Carbon::now()->format('d M Y') }}</b></td>
                        </tr>
                    </table>
                </div>
            </div>
            <p class="mb-0 mt-3">Cek dengan benar detail donasi Anda.</p>
            <p>Selanjutnya, segera klik tombol "Checkout" untuk melanjutkan ke proses pembayaran.</p>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h5>Pembayaran</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ url('order') . '/' . $child->child_id }}" class="mb-0">
                    @csrf
                    <input type="hidden" name="monthly_subscription" value={{$period}}>
                    <button class="btn btn-primary" id="pay-button">Checkout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('footer')
