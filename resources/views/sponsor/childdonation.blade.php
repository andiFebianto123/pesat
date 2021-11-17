@extends('sidebar')
@section('content')
<table class="table">
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
      <th scope="row">#{{$order->order_no}}</th>
      <td></td>

      <td>
        @if($order->payment_status==1)
        {{"pending"}}
        @elseif($order->payment_status=2)
        {{"suskes"}}
        @else
        {{"kadaluarsa"}}
      @endif
      </td>
      <td>{{$order->total_price}}</td>
      <td></td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
