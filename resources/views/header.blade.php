<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{isset($title) ? ($title . ' :: ') : ''}}Pesat</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom/styleku.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}">
<style>
  .logo {
    position: absolute;
    left: 50%;
    top:16px;
    background: #fff;
    width: 90px;
    height: 90px;
    border-radius: 50%;
    -webkit-box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 1px 2px rgb(0 0 0 / 24%);
    box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 4px 5px rgb(0 0 0 / 50%);
    padding-top: 10px;
    margin-left: -45px;
}
.logo__img{
    display: block;
    /* position: absolute; */
    width: 100%;
    opacity: 1;
    transition: .5s;
    -moz-transition: .5s;
    -webkit-transition: .5s;
}
</style>
</head>
<body>
<div class="navigation position-relative">
  <div class="pos-f-t">
    <nav class="navbar navbar-light bg-white">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div id="button-flex" style="order: 3;">
          @if(auth()->user() == null)
          <a href="{{route('sponsor.login')}}" style="text-decoration:none;color: inherit;">
              <button class="btn btn-outline-primary" type="submit">Login</button>
          </a>
          <a href="{{url('register')}}" id="register">
              <button class="btn btn-primary" type="submit">Register</button>
          </a>
          @else
          <a href="{{url('my-account')}}" style="text-decoration:none;color: inherit;">
              <button class="btn btn-primary" type="submit">Akun Saya</button>
          </a>
          @endif
      </div>
      </div>
    </nav>
    <div class="collapse navbar-collapse navbar-light" id="navbarToggleExternalContent">
      <div class="bg-white container-fluid">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="{{url('sponsor/home')}}">Home</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" aria-current="page" href="{{url('list-child')}}">Sponsor Anak</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" aria-current="page" href="{{url('list-proyek')}}">Sponsor Proyek</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" aria-current="page" href="{{url('donate-goods')}}">Sponsor Barang</a>
          </li>
        </ul>
        @if( auth()->user() == null )
          <!-- <a href="{{url('register')}}">
              <button class="btn btn-primary" type="submit">Register</button>
          </a> -->
          <a href="{{url('register')}}" id="register2">
            <button class="btn btn-primary mb-3" type="submit">Register</button>
          </a>          
        @endif
      </div>
    </div>
  </div>
  <div class="logo">
    <a href="{{url('/')}}"><img class ="logo__img" src="{{asset('images/logopesat.png')}}" alt="Logo"></a>
  </div>
</div>
<div style="height: 40px; display:block;"></div>
  
</br></br>