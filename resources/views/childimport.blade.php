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
                        <form action="{{ backpack_url('import') }}" class="" id="custom-form" method="POST" enctype="multipart/form-data">
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
                        <div class="form-group has-error">
                                <label for="formFile" class="form-label">File Anak</label>
                                <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="formFile" name="file" >
                                        <label class="custom-file-label" id="label-file-input" for="formFile">Choose file</label>
                                        <div class="invalid-feedback">File is not required</div>
                                </div>
                        </div>
                        <input type="submit" class="btn btn-primary btn-sm" value="Submit"/>
                        </form>
                </div>
            </div>
        </div>
</div>
@endsection

@section('after_styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
@endsection

@push('after_scripts')
  <script type="text/javascript" src="{{ asset('packages/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('packages/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('packages/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('packages/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('packages/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('packages/datatables.net-fixedheader-bs4/js/fixedHeader.bootstrap4.min.js') }}"></script>
        @include('bulk_error_table')
@endpush

@push('after_scripts')
        <script>
                crudBulkMessages.table = $('#crudTableBulkMessage').DataTable(crudBulkMessages.dataTableConfiguration);
                $('#modal-error-bulk').on('shown.bs.modal', function () {
                        crudBulkMessages.table.columns.adjust();
                });
                var inputLabel = $('#label-file-input');
                $('#formFile').change(function(){
                        console.log('enti');
                        var path = $(this).val();
                        var path = path.replace("C:\\fakepath\\", "");
                        inputLabel.html(path);
                });
                $(function(){
                        $('#custom-form').submit(function(e){
                                e.preventDefault();
                                var form = $(this);
                                var actionUrl = form.attr('action');
                                var dataForm = new FormData();

                                var files = $(this)[0][1].files;

                                if(files.length > 0){
                                        dataForm.append('file', files[0]);
                                }
                                $.ajax({
                                        type: "POST",
                                        url: actionUrl,
                                        data: dataForm, // serializes the form's elements.
                                        processData: false,  // tell jQuery not to process the data
                                        contentType: false,
                                        success: function(result){ 
                                                $(form)[0].reset();
                                                if(result.validator){
                                                        $('#formFile').addClass('is-invalid');
                                                }else{

                                                        $('#formFile').removeClass('is-invalid');

                                                        if(result.status){
                                                                inputLabel.html('');
                                                                new Noty({
                                                                        type: "success",
                                                                        text: result.notification,
                                                                }).show();
                                                        }else{
                                                                // jika ada data yang error
                                                                crudBulkMessages.table.clear();
                                                                $.each(result.data, function(index, value){
                                                                        crudBulkMessages.table.row.add(value);
                                                                });
                                                                crudBulkMessages.table.draw();
                                                                $('#modal-error-bulk').modal('show');
                                                                new Noty({
                                                                        type: "danger",
                                                                        text: result.notification,
                                                                }).show();
                                                                inputLabel.html('');
                                                        }
                                                }
                                                
                                        },
                                        error: function (xhr, desc, err)
                                        {
                                                console.log(err);
                                        }
                                });
                        });                   

                });
                
        </script>
@endpush
