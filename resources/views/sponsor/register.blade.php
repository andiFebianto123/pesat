@include('header')
<div class="container">
<form id="register" action="{{url('create-account')}}" method="post">
{{ csrf_field() }}
    </br>
    @if ($message = Session::get('success'))
        <div class="alert alert-success" role="alert">
            <strong>{{$message}}</strong>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="alert alert-danger" role="alert">
            <strong>{{$message}}</strong>
        </div>
    @endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="mb-3">
        <label>
            <strong>Nama Panggilan</strong>
        </label>
        <input type="text" name="name" placeholder="Nama Panggilan" value="{{old('name')}}" class="form-control"/>
    </div>
    <div class="mb-3">
        <label>
            <strong>Nama Lengkap</strong>
        </label>
        <input type="text" name="fullname" placeholder="Nama Lengkap" value="{{old('fullname')}}" class="form-control" required/>
    </div>
    <div class="mb-3">
        <label><strong>Tempat Lahir </strong></label>
        <input type="text" name="hometown" placeholder="Tempat Lahir" value="{{old('hometown')}}" class="form-control"/>
    </div>
    <div class="mb-3">
        <label><strong>Tanggal Lahir </strong></label>    
        <div class="row form-group">
            <div class="input-group date" id="datepicker">
                <input type="text" class="form-control" placeholder="Tanggal Lahir" name="dateofbirth" value="{{old('dateofbirth')}}">
                <div class="input-group-append">
                </div>
                <span class="input-group-text">
                    <i class="fa fa-calendar"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label><strong>Alamat</strong></label>
        <input type="text" name="address" value="{{old('address')}}" placeholder="Alamat" class="form-control"/>
    </div>
    <div class="mb-3">
        <label><strong>Email </strong></label>
        <input type="email" name="email" value="{{old('email')}}" placeholder="Email" class="form-control" required/>
    </div>
    <div class="mb-3">
        <label><strong>Password</strong></label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password">
    </div>

        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    <div class="mb-3">
        <label><strong>Ulangi Password</strong></label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password_confirmation">
    </div>    
    <div class="mb-3">
        <label><strong>No Ponsel / Whatsapp </strong></label>
        <input type="number" name="nohp" placeholder="No Hp / Whatsapp" value="{{old('nohp')}}" class="form-control" required/>
    </div>    
    <div class="mb-3">
        <label><strong>Jemaat Dari Gereja </strong></label>
        <input type="text" name="memberofchurch" placeholder="Nama Gereja" value="{{old('memberofchurch')}}" class="form-control"/>
    </div>    
    <div clas="col-2">
        <button type="submit" class="btn btn-primary">Daftar</button>
    </div>
</form>
</div>
@include('footer')