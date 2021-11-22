@include('header')
<div class="container">
    <form id="forgotpassword" action="{{url('reset-password')}}" method="post">
    {{ csrf_field() }}
    <div class="col-12">
        <div class="bs-callout bs-callout-primary">
            <h2>Lost Password</h2>
        </div>
        @if ($message = Session::get('error'))
        <div class="alert alert-danger" role="alert">
            <strong>{{$message}}</strong>
        </div>
        @endif
        @if ($message = Session::get('success'))
        <div class="alert alert-success" role="alert">
            <strong>{{$message}}</strong>
        </div>
        @endif
        
        Lost your password? Please enter your email address. You will receive new password via email.
    </div>
    </br>
    <div class="col-3">
        <input type="email" id="email" name="email" placeholder="Email" class="form-control" required/>
    </div>
    </br>
    <div clas="col-1">
        <button type="submit" class="btn btn-success">Reset Password</button>
    </div>
    </form>
</div>
@include('footer')