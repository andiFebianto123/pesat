@extends(backpack_view('blank'))
@section('content')
</br></br>
<h1>Dashboard</h1>

</br>
<div class="row">
    <div class="col-2">
        <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
        <div class="card-header" align="center">Jumlah Anak Yang Disponsori</div>
        <div class="card-body">
            <h5 class="card-title" align="center">{{$sponsored}}</h5>
    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
        </div>
        </div>
    </div>
    <div class="col-2">
        <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
        <div class="card-header" align="center">Jumlah Anak Yang Belum Disponsori</div>
        <div class="card-body">
        <h5 class="card-title" align="center">{{$notsponsored}}</h5>
    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
        </div>
        </div>
    </div>
    <div class="col-2">
        <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
        <div class="card-header" align="center">Jumlah Uang</div>
        <div class="card-body">
        <h5 class="card-title" align="center">Rp. {{number_format($totalamount, 2, ',', '.')}}</h5>
    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
        </div>
        </div>
    </div>

    <div class="col-2">
        <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
        <div class="card-header" align="center">Jumlah Sponsor Baru Bulan Ini</div>
        <div class="card-body">
        <h5 class="card-title" align="center">{{$newsponsor}}</h5>
    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
        </div>
        </div>
    </div>

    <div class="col-2">
        <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
        <div class="card-header" align="center">Sponsor Yang Belum Bayar</div>
        <div class="card-body">
        <h5 class="card-title" align="center">{{$notpaid}}</h5>
    <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
        </div>
        </div>
    </div>
</div>
@endsection
