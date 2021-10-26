<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Boostrap 5</title>
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap.min.css') }}>
    <link rel="stylesheet" href={{ asset('assets/font-awesome/css/font-awesome.css') }}>
    
    <style>
    body { background: #f8f8f8 !important; }
    header{
        background:#FFFFFF;
    }
   
    </style>
</head>
<body>
    <header >
        <div class='row'>
            <div class="col-10">
            </div>
            <div class="col-2">
            <button type="button" class="btn btn-primary" style="margin-top:10px">Masuk</button>
            </div>
        </div>
</br>
    </header>
<div class="container">
    <div class="col-12" >
       <h2>Sponsor Anak</h2>
       </br>
       <p>
       Kami melayani ribuan anak desa dan membimbing serta memperlengkapi mereka menjadi pemimpin masa depan bangsa. Untuk itu setiap anak 
       membutuhkan dukungan pembiayaan sebesar Rp.150.000/bulan. 80% digunakan untuk program pembinaan, 10% untuk administrasi dan 10% untuk upaya-
       upaya sosialisasi program pelayanan. Bila Bp/Ibu/Sdr tergerak untuk memberikan sponsorship bagi program pembinaan anak (Future Center), silakan mengisi 
       formulir di bawah ini:
       </p>
    </div>
    <form id="form-filter" action="{{url('/')}}" method="GET" >
   
    {!! csrf_field() !!}
    <div class="card card-body">
        <div class="row">
            <div class="col-4">
                <p>Provinsi :</p>
                    <select class="form-select" name = "provinceid" aria-label="Default select example">
                        <option selected></option>
                        @foreach ($provinces as $province)
                        <option value="{{$province->province_id}}">{{$province->province_name}}</option>
                        @endforeach

                    </select>
            </div>
            <div class='col-4'>
                <p>Gender :<p>
                    <select class="form-select" aria-label="Default select example">
                        <option selected></option>
                        <option value="1">Laki-Laki</option>
                        <option value="2">Perempuan</option>
                    </select>
            </div>
            <div class='col-4'>
                <p>Kelas :<p>
                    <select class="form-select" aria-label="Default select example">
                        <option selected></option>
                        @foreach($class as $key => $data) 
                        <option value="{{$key}}">{{$key}}</option>
                        @endforeach
                    </select>
            </div>
       </div>
       <div class="row">
            <div class="col-10">
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-primary" style="margin-top:10px;margin-left:80px">Apply Filter</button>
            </div>
           
        </div>
    </div>
    </form>
       </br>
    <div class="row">
    @foreach ($childs as $key => $child)
    <div class="col-4">
        <div class="card" style="width: 26rem; margin-bottom:30px;">
            <img class="card-img-top" src="{{asset('storage/'.$child->photo_profile)}}" alt="Card image cap">
            <div class="card-body">
                <h5 class="card-title">{{$child->full_name}}</h5>
            </div>
        </div>
       </div>
       
    @endforeach
   
   
    </div>
  
    
    </div>
    <div class="d-flex justify-content-center">
            {!! $childs->links() !!}
        </div>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
   
    <footer class="bg-dark text-center text-white">
        <div class="container p-4">
           <h2> CONTACT US</h2>
       </div>
       <div class="col-auto">
       Silakan mengirimkan e-mail, telepon, atau join facebook kami:
          </div>
    <section class="mb-4">

      <ul class="nav justify-content-center">
        <li class="nav-item">
            <a class="nav-link link-light" aria-current="page" href="#"><i class="fa fa-envelope"></i> pesatjkt@gmail.com</a>
        </li>
        <li class="nav-item">
            <a class="nav-link link-light" href="#"><i class="fa fa-facebook-square"></i> PESAT MINISTRY</a></a>
        </li>
        <li class="nav-item">
            <a class="nav-link link-light" href="#"><i class="fa fa-instagram"></i> pesat_ministry</a></a>
        </li>
        <li class="nav-item">
            <a class="nav-link link-light" href="#" tabindex="-1" aria-disabled="true"><i class="fa fa-phone"></i> 0821 1462 2245</a>
        </li>
      </ul>
    
    </section>

            Copyright © 2017 PESAT. All rights reserved. | Syarat & Ketentuan
       </footer>
      
</body>
</html>