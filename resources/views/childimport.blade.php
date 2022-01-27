@extends(backpack_view('blank'))

@section('header')
	<section class="container-fluid">
	  <h2>
           <span class="text-capitalize">Import Anak</span>
	  </h2>
	</section>
@endsection

@section('content')
<!-- <h1>Import Anak</h1> -->
<div class="row">
        <div class="col-md-8 bold-labels">
            <div class="card">
                <div class="card-body">
                        <form action="{{ backpack_url('import') }}" method="POST" enctype="multipart/form-data">
                        @if($message = Session::get('success'))
                                <div class="col- 5 alert alert-success" role="alert">
                                {{$message}}
                                </div>
                        @endif

                        @if($message = Session::get('error'))
                                <div class="col-5 alert alert-danger" role="alert">
                                {{$message}}
                                </div>
                        @endif
                        {{ csrf_field() }}
                        <div class="form-group">
                                <label for="formFile" class="form-label">File Anak</label>
                                <div class="custom-file">
                                <input type="file" class="custom-file-input" id="formFile" name="file">
                                <label class="custom-file-label" id="label-file-input" for="formFile">Choose file</label>
                                </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                        </form>
                </div>
            </div>
        </div>
</div>
@endsection

@section('after_scripts')
        <script>
                $(function(){
                        var fileInput = $('#formFile');
                        var inputLabel = $('#label-file-input');
                        fileInput.change(function() {
                                var path = $(this).val();
                                var path = path.replace("C:\\fakepath\\", "");
                                inputLabel.html(path);
                        });
                });
        </script>
@endsection
