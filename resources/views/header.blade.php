<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PESAT</title>
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap/bootstrap.min.css') }}>
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap/bootstrap.css') }}>
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap/bootstrap-datepicker.min.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('assets/css/custom/styleku.css') }}>
    <link rel="stylesheet" href={{ asset('assets/font-awesome/css/font-awesome.css') }}>
    <link rel="stylesheet" href={{ asset('assets/font-awesome/css/font-awesome.min.css') }}>
    <script src="{{asset('assets/js/custom/jquery.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-datepicker.min.js')}}"></script>
<style>
  .logo {
    left: 50%;
    background: #fff;
    width: 90px;
    height: 90px;
    border-radius: 50%;
    -webkit-box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 1px 2px rgb(0 0 0 / 24%);
    box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 4px 5px rgb(0 0 0 / 50%);
    margin-left: -45px;
    position:absolute;
    top:40px;
}
.logo__img{
    display: block;
    position: absolute;
    left: 0;
    top: 10px;
    width: 100%;
    opacity: 1;
    transition: .5s;
    -moz-transition: .5s;
    -webkit-transition: .5s;
}
</style>
</head>
<body>
    <header >

    <nav class="navbar navbar-expand-lg navbar-light bg-white">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{url('sponsor/home')}}">Home</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <div class="col-5">
        </div>
        <div class="col-4">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li>
          <a class="navbar-brand" aria-current="page" href="{{url('list-child')}}">Sponsor Anak</a>
        </li>
        <li>
          <a class="navbar-brand" aria-current="page" href="{{url('list-proyek')}}">Sponsor Proyek</a>
        </li>
        <li>
          <a class="navbar-brand" aria-current="page" href="{{url('donate-goods')}}">Sponsor Barang</a>
        </li>
      </ul>
        </div>
     
    </div>
    <!-- <form> -->
    @if( auth()->user() == null )
    <a href="{{route('sponsor.login')}}" style="text-decoration:none;color: inherit;">
        <button class="btn btn-outline-primary" type="submit">Login</button>
    </a>&nbsp
    <a href="{{url('register')}}">
        <button class="btn btn-outline-primary" type="submit">Register</button>
    </a>
    @else
    <a href="{{url('my-account')}}" style="text-decoration:none;color: inherit;">
        <button class="btn btn-outline-primary" type="submit">Akun Saya</button>
    </a>&nbsp
    @endif
      <!-- </form> -->
  </div>
</nav>
</br>
<a class="logo">
<img class ="logo__img" src="{{asset('images/logopesat.png')}}" alt="Logo">
</a>
</header>
</br></br>