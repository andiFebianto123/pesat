@extends(backpack_view('blank'))
@section('content')
</br></br>
<h1>Import Anak</h1>

</br>

<div class="row">
<form action="{{ backpack_url('import') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
