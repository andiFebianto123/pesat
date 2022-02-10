@component('mail::custom_layout')
@slot('header')
@component('mail::header', ['url' => ''])
@endcomponent
@endslot

@slot('slot')
<h1 style="background-color:#2196f3;margin-bottom:0px;color:white;padding:24px">{{$title}}</h1>
<div style="padding: 32px">
    Hi, {{$sponsor->full_name}}
    <p></p>
    Terima kasih telah bergabung di layanan PESAT untuk sponsor Anak.
    <p style="margin:0px"></p>
    Beserta dengan email ini, kami ingin mengingatkan Anda untuk melakukan pembayaran untuk melanjutkan sponsor Anak yang sudah Anda pilih.
    <p></p>
    Here are the details of your order placed on {{$date}}:
    <h2 style="color:#2196f3;margin-top:24px">{{$titleOrder}}</h2>
    <div class="table">
        <table>
            <thead>
                <tr>
                    <th align="left">Donate</th>
                    <th>Quantity</th>
                    <th align="right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderDetails as $orderDetail)
                    <tr>
                        <td align="left">{{$orderDetail->childname->full_name}} - {{$orderDetail->monthly_subscription}} Bulan</td>
                        <td align="center">1</td>
                        <td align="right">Rp {{number_format($orderDetail->price, 2, ',', '.')}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2">Subtotal:</td>
                    <td align="right">Rp {{$subtotal}}</td>
                </tr>
                <tr>
                    <td colspan="2">Payment Method:</td>
                    <td align="right">{{$payment_method}}</td>
                </tr>
                <tr>
                    <td colspan="2">Total:</td>
                    <td align="right">Rp {{$total}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <h2 style="color:#2196f3;margin-top:24px">Billing Address</h2>
    <div style="border: 1px solid #edeff2;padding:8px;margin-bottom:32px">
        {{$sponsor->full_name}}
        <p></p>
        {{$sponsor->address}}
        <p></p>
        {{$sponsor->no_hp}}
        <p></p>
        {{$sponsor->email}}
    </div>
    Mohon untuk segera melakukan pembayaran sebelum tanggal berakhirnya masa sponsor.
    <p style="margin: 0px"></p>
    Abaikan email ini jika Anda sudah melakukan pembayaran.
    <p></p>
    @if ($lastReminder)
    Ini adalah pengingat terakhir untuk pembayaran donasi anak di pesat. Harap melakukan pembayaran maksimal tanggal <strong>{{Carbon\Carbon::parse($orderDetails->first()->start_order_date)->format('F d, Y')}}</strong>.
    <p style="margin: 0px"></p>
    Jika lebih dari tenggang waktu pembayaran diatas maka pembayaran tidak dapat dilakukan.
    <p></p>
    @endif
    Terima kasih
    <div style="text-align: center;margin-top:24px">
        <a href="{{url('/')}}" style="display: inline-block;text-decoration:none;color:#2196f3">{{config('app.name')}}</a>
        <p style="margin:0px;"></p>
        Developed by <a href="https://rectmedia.com" style="display: inline-block;color:#2196f3">RECTmedia</a>
    </div>
</div>
@endslot
@slot('footer')
@component('mail::footer')
@endcomponent
@endslot
@endcomponent