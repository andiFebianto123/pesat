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
  @foreach($projectorders as $key => $projectorder)
    <tr>
      <th scope="row">#{{$projectorder->order_project_id}}</th>
      <td>{{date('d-m-Y', strtotime($projectorder->created_at))}}</td>

      <td>
        @if($projectorder->payment_status==1)
        {{"Menunggu Pembayaran"}}
        @elseif($projectorder->payment_status==2)
        {{"Sukses"}}
        @else
        {{"Batal"}}
      @endif
      </td>
      <td>Rp{{ number_format($projectorder->price, 2, ',', '.') }}</td>
      <td>
      <a href="{{url('project-donation-detail/'.$projectorder->order_project_id)}}" class="btn btn-sm btn-primary" role="submit">
        Detail
      </a>
      @if($projectorder->payment_status==1)
          <a href="{{url('checkout-order-project/' . $projectorder->order_project_id)}}" class="btn btn-sm btn-primary" role="submit">
            Pay  
          </a>
      @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
</div>
<div class="d-flex justify-content-center">
    {!! $projectorders->links() !!}
</div>
@endsection
