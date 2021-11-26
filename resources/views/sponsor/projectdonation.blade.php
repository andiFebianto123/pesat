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
  @foreach($projectorders as $key => $projectorder)
    <tr>
      <th scope="row">#{{$projectorder->order_project_id}}</th>
      <td>{{date('d-m-Y', strtotime($projectorder->created_at))}}</td>

      <td>
        @if($projectorder->payment_status==1)
        {{"menunggu pembayaran"}}
        @elseif($projectorder->payment_status==2)
        {{"suskes"}}
        @else
        {{"kadaluarsa"}}
      @endif
      </td>
      <td>{{$projectorder->price}}</td>
      <td>
      <a href="{{url('project-donation-detail/'.$projectorder->order_project_id)}}">
        <button class="btn btn-outline-info" type="submit">Detail</button>
      </a>
@if($projectorder->payment_status==1)
      <a href="{{url('project-order/'.$projectorder->snap_token.'/'.$projectorder->order_project_id)}}">
        <button class="btn btn-outline-info" type="submit">Pay</button>
      </a>
@endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
