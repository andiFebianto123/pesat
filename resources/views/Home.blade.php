@include('header')
<div class="container">
        <div class="col-12" >
            <div class="bs-callout bs-callout-primary">
                <h2>Donation</h2>
            </div>
        </div>
       </br>
       <p>
        Ayo ikut berpartisipasi dalam melayani anak-anak miskin di daerah terpencil. Pilih bentuk donasi mu di bawah ini:
        </p>
        </br>       
        <div class="row">
    
            <div class="col-4">
                <a href="{{url('list-child')}}" style="text-decoration:none;color: inherit;">
                        <div class="card" style="width: 23rem;margin-left:25px; margin-bottom:30px;">

                            <img class="card-img-top" src="{{asset('images/donasianak.jpg')}}" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title">Sponsor Anak</h5>
                            </div>
                        </div>
                </a>
            </div>

            <div class="col-4">
                <a href="{{url('list-proyek')}}" style="text-decoration:none;color: inherit;">
                        <div class="card" style="width: 23rem;margin-left:25px; margin-bottom:30px;">

                            <img class="card-img-top" src="{{asset('images/sponsorproyek.jpg')}}" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title">Sponsor Proyek</h5>
                            </div>
                        </div>
                </a>
            </div>

            <div class="col-4">
                <a href="{{url('donate-goods')}}" style="text-decoration:none;color: inherit;">
                        <div class="card" style="width: 23rem;margin-left:25px; margin-bottom:30px;">

                            <img class="card-img-top" src="{{asset('images/barang.jpg')}}" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title">Sponsor Barang</h5>
                            </div>
                        </div>
                </a>
            </div>
       

    
        </div>

</div>
@include('footer')