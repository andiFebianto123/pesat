@extends(backpack_view('blank'))
@section('content')
    </br></br>
    <h1>Dashboard</h1>

    </br>
    <div class="row mb-3">
        <div class="col-md-6">
            <h5>Data Bulan {{ $monthYear }}</h5>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-dark bg-light mb-0 h-100">
                <div class="card-header" >
                    <div class="d-flex align-items-center justify-content-center text-break text-center overflow-hidden" style="height: 60px;">
                        Jumlah Anak Yang Disponsori
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center text-break text-center overflow-hidden" style="height: 100px;">
                        <h5 class="card-title" >{{ $sponsored }}</h5>
                    </div>
                    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-dark bg-light mb-0 h-100">
                <div class="card-header" >

                    <div class="d-flex align-items-center justify-content-center text-break text-center overflow-hidden" style="height: 60px;">
                        Jumlah Anak Yang Belum Disponsori
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center text-break text-center overflow-hidden" style="height: 100px;">
                        <h5 class="card-title" >{{ $notsponsored }}</h5>
                    </div>
                    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-dark bg-light mb-0 h-100">
                <div class="card-header" >
                    <div class="d-flex align-items-center justify-content-center text-break text-center overflow-hidden" style="height: 60px;">
                        Jumlah Uang
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center text-break text-center overflow-hidden" style="height: 100px;">
                        <h5 class="card-title" >Rp. {{ number_format($totalamount, 2, ',', '.') }}</h5>
                    </div>
                    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-dark bg-light mb-0 h-100">
                <div class="card-header" >
                    <div class="d-flex align-items-center justify-content-center text-break text-center overflow-hidden" style="height: 60px;">
                        Jumlah Sponsor Baru Bulan Ini
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title" >
                        <div class="d-flex align-items-center justify-content-center text-break text-center overflow-hidden"
                            style="height: 100px;">
                            {{ $newsponsor }}
                        </div>
                    </h5>
                    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card text-dark bg-light mb-0 h-100">
                <div class="card-header" >
                    <div class="d-flex align-items-center justify-content-center text-break text-center overflow-hidden" style="height: 60px;">
                        Sponsor Yang Belum Bayar
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title" >
                        <div class="d-flex align-items-center justify-content-center text-break text-center overflow-hidden"
                            style="height: 100px;">
                            {{ $notpaid }}
                        </div>
                    </h5>
                    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                </div>
            </div>
        </div>
    </div>
@endsection
