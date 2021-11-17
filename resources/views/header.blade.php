<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PESAT</title>
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap/bootstrap.min.css') }}>
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap/bootstrap.css') }}>
    <link rel="stylesheet" href={{ asset('assets/css/bootstrap/bootstrap-datepicker.min.css') }}>
    <link rel="stylesheet" href={{ asset('assets/css/custom/styleku.css') }}>
    <link rel="stylesheet" href={{ asset('assets/font-awesome/css/font-awesome.css') }}>
    <link rel="stylesheet" href={{ asset('assets/font-awesome/css/font-awesome.min.css') }}>
    <script src="{{asset('assets/js/custom/jquery.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-datepicker.min.js')}}"></script>

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
                                 document.getElementById('logout-form').submit();"> -->
                <a href="{{url('my-account')}}">
                <button type="button" class="btn btn-primary" style="margin-top:10px">Akun Saya</button>
                </a>
    
                </div>

                @endif
            
        </div>
 
    </br>
    </header>