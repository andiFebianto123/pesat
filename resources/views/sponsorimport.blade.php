@extends(backpack_view('blank'))
@section('content')
</br></br>
<h1>Import Sponsor</h1>

</br>

<div class="row">

<form action="{{ backpack_url('importsponsor') }}" method="POST" enctype="multipart/form-data">
                @csrf

@if($message = Session::get('success'))
        <div class="alert alert-success" role="alert">
        {{$message}}
        </div>
@endif

@if($message = Session::get('error'))
        <div class="alert alert-danger" role="alert">
            {{$message}}
        </div>
@endif

    <div class="mb-3">
        <input class="form-control" type="file" name = "file" id="formFile">
    </div>
    &nbsp
    <div class="mb-3">
        <button type="submit" class="btn btn-primary mb-3">Submit</button>
    </div>
</form>
</div>
@endsection
