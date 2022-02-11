@extends(backpack_view('blank'))

@section('content')
    <?php
    function rupiah($angka)
    {
        $hasil_rupiah = 'Rp ' . number_format($angka, 2, ',', '.');
        return $hasil_rupiah;
    }
    ?>
    <ol class="breadcrumb bg-transparent p-0 justify-content-end">
        <li class="breadcrumb-item text-capitalize"><a href="{{ url('admin') }}">Admin</a></li>
        <li class="breadcrumb-item text-capitalize"><a href="{{ url('admin/sponsor-donation') }}">Sponsor Donation</a></li>
        <li class="breadcrumb-item text-capitalize active" aria-current="page">List</li>
    </ol>
    <div id="sponsorDonation">
        <h2>
            <span class="text-capitalize">Data Donasi Sponsor</span>
            <small id="datatable_info_stack" class="animated fadeIn" style="display: inline-flex;">
                <div class="dataTables_info" id="crudTable_info" role="status" aria-live="polite">Showing 1 to 3 of 3
                    entries.</div><a href="http://localhost:8080/admin/child-master" class="ml-1"
                    id="crudTable_reset_button">Reset</a>
            </small>
        </h2>
        <div class="row mb-0 dataTables_wrapper">
            <div class="col-sm-6">

            </div>
            <div class="col-sm-6">
                <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none">
                    <div id="crudTable_filter" class="dataTables_filter">
                        <label>
                            <input type="search" class="form-control" placeholder="Search..." aria-controls="crudTable">
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable ">
                <thead>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Kota</th>
                        <th scope="col">Jumlah Order</th>
                        <th scope="col">Jumlah Donasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $val)
                        <tr>
                            <td>{{ $val['sponsor_name'] }} </td>
                            <td>{{ $val['hometown'] }}</td>
                            <td>{{ $val['total_order'] }} </td>
                            <td>Rp. {{ number_format($val['total_price'], 2, ',', '.') }} </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Kota</th>
                        <th scope="col">Jumlah Order</th>
                        <th scope="col">Jumlah Donasi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row mt-2 d-print-none ">
            <div class="col-sm-12 col-md-4">
                <div class="dataTables_length" id="crudTable_length"><label>
                        <select name="crudTable_length" aria-controls="crudTable"
                            class="custom-select custom-select-sm form-control form-control-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                        </select> entries per page</label></div>
            </div>
            <div class="col-sm-0 col-md-4 text-center"></div>
            <div class="col-sm-12 col-md-4 ">
                <div class="dataTables_paginate paging_simple_numbers" id="crudTable_paginate">
                    <ul class="pagination">
                        <li class="paginate_button page-item previous disabled" id="crudTable_previous"><a href="#"
                                aria-controls="crudTable" data-dt-idx="0" tabindex="0" class="page-link">&lt;</a></li>
                        <li class="paginate_button page-item active"><a href="#" aria-controls="crudTable" data-dt-idx="1"
                                tabindex="0" class="page-link">1</a></li>
                        <li class="paginate_button page-item next disabled" id="crudTable_next"><a href="#"
                                aria-controls="crudTable" data-dt-idx="2" tabindex="0" class="page-link">&gt;</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('after_styles')
    <!-- DATA TABLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('packages/backpack/crud/css/crud.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('packages/backpack/crud/css/form.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('packages/backpack/crud/css/list.css') }}">
    <style>
        #sponsorDonation div.dataTables_length select {
            display: inline-block;
            width: auto;
            min-width: 3.6rem;
            margin-right: 0.4rem;
        }

        #sponsorDonation div.dataTables_paginate ul.pagination {
            margin: 2px 0;
            white-space: nowrap;
            justify-content: flex-end;
        }

        #sponsorDonation .dataTables_filter input {
            border-radius: 25px;
        }

    </style>

@endsection
@section('after_scripts')
    <script>
        var data = {!! json_encode($data) !!};
        console.log(data)
    </script>
@endsection
