@extends('sidebar')
@section('content')
<table class="table">
  <thead>
    <tr>
      <th scope="col">No</th>
      <th scope="col">File</th>
      <th scope="col">Tanggal Upload</th>
      <th scope="col">Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($dlp as $key => $listdlp)
    <tr>
      <th scope="row">{{$key+1}}</th>
      <td><a href="{{url('storage/'.$listdlp->file_dlp)}}">File pdf</a></td>
      <td>{{date('d-m-Y', strtotime($listdlp->created_at))}}</td>
      <td>
      <a href="{{url('storage/'.$listdlp->file_dlp)}}" download>
        <button class="btn btn-outline-info" type="submit">Download</button>
      </a>
      
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
<div class="d-flex justify-content-center">
            {!! $dlp->links() !!}
    </div>
@endsection
