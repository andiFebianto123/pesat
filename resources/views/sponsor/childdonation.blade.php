@extends('sidebar')
@section('content')
@if ($message = Session::get('error'))
    <div class="alert alert-danger" role="alert">
        <strong>{{$message}}</strong>
    </div>
@endif
<div class="table-responsive">
  <table class="table text-nowrap">
    <thead>
      <tr>
        <th scope="col">Order</th>
        <th scope="col">Date</th>
        <th scope="col">Status</th>
        <th scope="col">Total</th>
        <th scope="col">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $key => $order)
      <tr>
        <th scope="row">#{{$order->order_id}}</th>
        <td>{{date('d-m-Y', strtotime($order->created_at))}}</td>
        <td>
          @if($order->payment_status == 1)
          {{"Menunggu Pembayaran"}}
          @elseif($order->payment_status  == 2)
          {{"Suskes"}}
          @else
          {{"Batal"}}
        @endif
        </td>
        <td>Rp{{ number_format($order->total_price, 2, ',', '.') }}</td>
        <td>
          <div>
            <a class="btn btn-sm btn-primary" href="{{url('child-donation-detail/'.$order->order_id)}}" role="button">Detail</a>
            @if ($order->payment_status == 1)
              <a class="btn btn-sm btn-primary"  href="{{url('order/' . $order->order_id)}}">Pay</a>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="d-flex justify-content-center">
            {!! $orders->links() !!}
    </div>
@endsection
