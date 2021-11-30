@extends('sidebar')
@section('content')


@if ($message = Session::get('success'))
        <div class="alert alert-success" role="alert">
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
<form id="form-edit-profile" action="{{url('update-account')}}" method="post">
{{ csrf_field() }}

<input type="hidden" name="sponsorid" class="form-control" value="{{$profile->sponsor_id}}">

<label class="form-label"><strong>Nama</strong></label>
<input type="text" name="fullname" class="form-control" value="{{$profile->full_name}}">
</br>
<label class="form-label"><strong>Tempat Lahir</strong></label>
<input type="text" name="hometown" class="form-control" value="{{$profile->hometown}}">
</br>
<!-- <label class="form-label"><strong>Tanggal Lahir</strong></label> -->
<!-- <input type="text" name="dateofbirth" class="form-control" value="{{$profile->date_of_birth}}"> -->
<label for="date" class="form-label"><strong>Tanggal Lahir</strong></label>
<div class="row form-group">
                
                    <div class="input-group date" id="datepicker">
                        <input type="text" class="form-control" name="dateofbirth" value="{{$profile->date_of_birth}}">
                        <span class="input-group-append">
                            <span class="input-group-text bg-white d-block">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </span>
                    </div>
                </div>
</br>
<label class="form-label"><strong>Alamat</strong></label>
<input type="text" name="address" class="form-control" value="{{$profile->address}}">
</br>
<label class="form-label"><strong>Email</strong></label>
<input type="text" name="email" class="form-control" value="{{$profile->email}}">
</br>
<label for="password" class="col-md-4 col-form-label text-md-right"><strong>Password</strong></label>
<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password">
</br>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

<label for="password" class="col-md-4 col-form-label text-md-right"><strong>Confirm Password</strong></label>
<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password_confirmation">
</br>
<label class="form-label"><strong>No Ponsel / Whatsapp</strong></label>
<input type="text" name="nohp" class="form-control" value="{{$profile->no_hp}}">
</br>
<label class="form-label"><strong>Jemaat Dari Gereja</strong></label>
<input type="text" name="churchmember" class="form-control" value="{{$profile->church_member_of}}">

</br>
<button type="submit" class="btn btn-primary">Update</button>
</form>
@endsection
