@extends(backpack_view('blank'))

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">Import Sponsor</span>
        </h2>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <a href="{{ backpack_url('download-sponsor') }}" class="btn btn-success mb-2">Download Template
                Sponsor</a>
            <div class="card">
                <div class="card-body">
                    <p><span class="font-weight-bold">INFO</span> : Apabila ID tidak ditemukan maka akan dibuat data Sponsor baru.</p>
                    <p class="mb-0"><span class="font-weight-bold">REQUIRED FIELD</span> : </p>
                    <p class="mb-0">1. Nama</p>
                    <p class="mb-0">2. Tempat Lahir</p>
                    <p class="mb-0">3. Tanggal Lahir</p>
                    <p class="mb-0">4. Alamat</p>
                    <p class="mb-0">5. User Email</p>
                    <p class="mb-0">6. No Ponsel Whatsapp</p>
                    <p>7. Jemaat dari Gereja</p>
                    <form action="{{ backpack_url('importsponsor') }}" class="" id="custom-form"
                        method="POST" enctype="multipart/form-data">
                        @if ($message = Session::get('success'))
                            <div class="col- 5 alert alert-success" role="alert">
                                {{ $message }}
                            </div>
                        @endif

                        @if ($message = Session::get('error'))
                            <div class="col-5 alert alert-danger" role="alert">
                                {{ $message }}
                            </div>
                        @endif
                        {{ csrf_field() }}
                        <div class="form-group has-error">
                            <label for="formFile" class="form-label">File Sponsor</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="formFile" name="file">
                                <label class="custom-file-label" id="label-file-input" for="formFile">Choose file</label>
                                <div class="invalid-feedback" id="formFileError"></div>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary btn-sm" id="formSubmit" value="Submit" />
                    </form>
                    <img id="loading-image" style="position: absolute;
                                                                top: 50%;
                                                                left: 50%;
                                                                transform: translate(-50%, -50%);
                                                                z-index: 10;
                                                                display:none;
                                                            "
                        src="{{ url('/packages/backpack/crud/img/ajax-loader.gif') }}" alt="Processing...">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_styles')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
@endsection

@push('after_scripts')
    <script type="text/javascript" src="{{ asset('packages/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('packages/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}">
    </script>
    <script type="text/javascript"
        src="{{ asset('packages/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('packages/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('packages/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('packages/datatables.net-fixedheader-bs4/js/fixedHeader.bootstrap4.min.js') }}"></script>
    @include('bulk_error_table')
@endpush

@push('after_scripts')
    <script>
        crudBulkMessages.table = $('#crudTableBulkMessage').DataTable(crudBulkMessages.dataTableConfiguration);
        $('#modal-error-bulk').on('shown.bs.modal', function() {
            crudBulkMessages.table.columns.adjust();
        });
        var inputLabel = $('#label-file-input');
        $('#formFile').change(function() {
            var path = $(this).val();
            var path = path.replace("C:\\fakepath\\", "");
            inputLabel.html(path);
        });
        $(function() {
            $('#custom-form').submit(function(e) {
                e.preventDefault();
                $('#formSubmit').attr('disabled', 'disabled');
                var form = $(this);
                var actionUrl = form.attr('action');
                var dataForm = new FormData();

                var files = $(this)[0][1].files;

                if (files.length > 0) {
                    dataForm.append('file', files[0]);
                }
                $('#loading-image').show();
                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: dataForm, // serializes the form's elements.
                    processData: false, // tell jQuery not to process the data
                    contentType: false,
                    success: function(result) {
                        $(form)[0].reset();
                        $('#loading-image').hide();
                        $('#formSubmit').removeAttr('disabled');
                        if (result.validator) {
                            $('#formFile').addClass('is-invalid');
                            $('#formFileError').html(result.message);
                        } else {

                            $('#formFile').removeClass('is-invalid');

                            if (result.status) {
                                inputLabel.html('');
                                new Noty({
                                    type: "success",
                                    text: result.notification,
                                }).show();
                            } else {
                                // jika ada data yang error
                                crudBulkMessages.table.clear();
                                $.each(result.data, function(index, value) {
                                    crudBulkMessages.table.row.add(value);
                                });
                                crudBulkMessages.table.draw();
                                $('#modal-error-bulk').modal('show');
                                new Noty({
                                    type: "warning",
                                    text: result.notification,
                                }).show();
                                inputLabel.html('');
                            }
                        }

                    },
                    error: function(xhr, desc, err) {
                        $('#loading-image').hide();
                        $('#formSubmit').removeAttr('disabled');
                        new Noty({
                            type: 'danger',
                            text: err
                        }).show();
                    }
                });
            });

        });
    </script>
@endpush
