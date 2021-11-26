@extends('sidebar')
@section('content')

<?php
if($orders->payment_status == 1){
    $status = 'pending';
}elseif($orders->payment_status ==2){
    $status = 'success';
}else{
    $status = 'cancel';
}
?>


<p>Order {{$orders->order_id}} was placed on {{date("Y-m-d", strtotime($orders->created_at))}} and is currently {{$status}}</p>
<h5 class="card-title">Detail Donasi</h5>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Donasi</th>
      <th scope="col">Total</th>
    </tr>
  </thead>
  <tbody>
 
    <tr>
      <td>{{$orders->title}}</td>
      <td>{{$orders->price}}</td>
    </tr>
   <tr>
        <th>Payment Method</th>
        <th>{{$orders->payment_type}}</th>
    </tr>
    <tr>
        <th>Total</th>
        <th>{{$orders->price}}</th>
    </tr>
  </tbody>
</table>
</br>
<h5 class="card-title">Billing Address</h5>

<div class="card" style="background:#f8f8f8">
  <div class="card-body">
    <h6 class="card-subtitle mb-2 text-muted">{{$orders->full_name}}</h6>
    <h6 class="card-subtitle mb-2 text-muted">{{$orders->address}}</h6>
    <h6 class="card-subtitle mb-2 text-muted">{{$orders->no_hp}}</h6>
    <h6 class="card-subtitle mb-2 text-muted">{{$orders->email}}</h6>
  </div>
</div>

@endsection