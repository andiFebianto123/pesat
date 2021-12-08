
@extends('sidebar')
@section('content')

<?php
    if($orderhd->payment_status == 1){
    
      $status = 'pending';
    
    }elseif($orderhd->payment_status == 2){
    
      $status = 'success';
    
    }else{

      $status = 'cancel';
    }
?>

<p>Order {{$orderhd->order_id}} was placed on {{date("Y-m-d", strtotime($orderhd->created_at))}} and is currently {{$status}}</p>
<h5 class="card-title">Detail Donasi</h5>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Donasi</th>
      <th scope="col">Total</th>
      <th scope="col">action</th>
    </tr>
  </thead>
  <tbody>
  @foreach($orders as $key => $order)
    <tr>
      <td>{{$order->child_name."- 1 bulan x ".$order->monthly_subscription}}</td>
      <td>{{$order->price_dt}}</td>
      <td>
      <a href="{{url('list-dlp/'.$order->child_id)}}">
        <button class="btn btn-outline-info" type="submit">List DLP</button>
        </a>
      </td>
    </tr>
@endforeach
    <tr>
        <th>Payment Method</th>
        <th>{{$order->payment_type}}</th>
    </tr>
    <tr>
        <th>Total</th>
        <th>{{$order->total_price}}</th>
    </tr>
  </tbody>
</table>
</br>


<h5 class="card-title">Billing Address</h5>

<div class="card" style="background:#f8f8f8">
  <div class="card-body">
    <h6 class="card-subtitle mb-2 text-muted">{{$order->sponsor_name}}</h6>
    <h6 class="card-subtitle mb-2 text-muted">{{$order->sponsor_address}}</h6>
    <h6 class="card-subtitle mb-2 text-muted">{{$order->no_hp}}</h6>
    <h6 class="card-subtitle mb-2 text-muted">{{$order->email}}</h6>
  </div>
</div>
@endsection