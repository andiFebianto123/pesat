
@include('header')
    <div class="container pb-5 pb-md-0">
        <div class="col-12" >
            <div class="bs-callout bs-callout-primary">
                <h2>Sponsor Anak</h2>
            </div>
            </br>
            <p>
                Kami melayani ribuan anak desa dan membimbing serta memperlengkapi mereka menjadi pemimpin masa depan bangsa. Untuk itu setiap anak 
                membutuhkan dukungan pembiayaan sebesar Rp.150.000/bulan. 80% digunakan untuk program pembinaan, 10% untuk administrasi dan 10% untuk upaya-
                upaya sosialisasi program pelayanan. Bila Bp/Ibu/Sdr tergerak untuk memberikan sponsorship bagi program pembinaan anak (Future Center), silakan mengisi 
                formulir di bawah ini:
            </p>
        </div>
        @if ($message = Session::get('error'))
            <div class="alert alert-danger" role="alert">
                <strong>{{$message}}</strong>
            </div>
        @endif
        <form id="form-filter" action="{{url('/list-child')}}" method="GET" >
   
        {!! csrf_field() !!}
        <div class="card card-body">
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <p>Provinsi :</p>
                        <select class="form-select" name = "provinceid" aria-label="Default select example">
                            <option selected></option>
                            @foreach ($provinces as $province)
                            <option value="{{$province->province_id}}">{{$province->province_name}}</option>
                            @endforeach
                        </select>
                </div>
                <div class='col-md-6 col-lg-4'>
                    <p>Gender :<p>
                        <select class="form-select" name="gender" aria-label="Default select example">
                            <option selected></option>
                            <option value="1">Laki-Laki</option>
                            <option value="2">Perempuan</option>
                        </select>
                </div>
                <div class='col-md-6 col-lg-4'>
                    <p>Kelas :<p>
                        <select class="form-select" name = "class" aria-label="Default select example">
                            <option selected></option>
                            @foreach($class as $key => $data) 
                            <option value="{{$data}}">{{$data}}</option>
                            @endforeach
                        </select>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary" style="margin-top:10px">Apply</button>
            </div>
        </div>
    </form>
       </br>
       
        <div class="row">
            @foreach ($childs as $key => $child)
    
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{url('childdetail/'.$child->child_id)}}" style="text-decoration:none;color: inherit;">
                    <input type="hidden" name="childid" value="{{$child->child_id}}">
                        <div class="card">
                            @if($child->photo_profile == '')
                            <img class="card-img-top" src="{{asset('images/blank.png')}}" alt="Card image cap">
                            @else
                            <img class="card-img-top" src="{{asset('storage/'.$child->photo_profile)}}" alt="Card image cap">
                            @endif
                            <div class="card-body">

                                    <h5 class="card-title">{{$child->full_name}}</h5>
                                    
                                    @if($child->is_sponsored == true)
                                    <p class="text-danger">Tersponsori</p>
                                    @endif
                                    
                            </div>
                        
                        </div>
                </a>
            </div>
       
        @endforeach
    
        </div>
    <div class="d-flex justify-content-center">
            {!! $childs->links() !!}
    </div>

</div>

    @include('footer')