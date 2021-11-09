<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PESAT</title>
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap/bootstrap.min.css') }}>
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap/bootstrap.css') }}>
    <link rel="stylesheet" href={{ asset('assets/css/styleku.css') }}>
    <link rel="stylesheet" href={{ asset('assets/font-awesome/css/font-awesome.css') }}>
    <script src="{{asset('assets/js/custom/jquery.min.js')}}"></script>
   
</head>
<body>
    <header >
        <div class='row'>
            <div class="col-10">
            </div>
            @if( Session::get('key')==null )
            <div class="col-2">
            
                <a href="{{route('sponsor.login')}}" style="text-decoration:none;color: inherit;">                    
                    <button type="button" class="btn btn-primary" style="margin-top:10px">Masuk</button>                
                </a>
            </div>
                @else
                <div class="col-2">
                <!-- <a href="{{route('sponsor.logout')}}" style="text-decoration:none;color: inherit;"   onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                <button type="button" class="btn btn-primary" style="margin-top:10px">Akun Saya</button>
                </a> -->
                <div class="dropdown">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                    Akun Saya
                    </a>

                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="{{route('sponsor.logout')}}" onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">Logout</a></li>
                    </ul>
                </div>
                    <form id="logout-form" action="{{ route('sponsor.logout') }}" method="POST" style="display: none;">
                    @csrf
                    </form>
                </div>

                @endif
            
        </div>
 
    </br>
    </header>