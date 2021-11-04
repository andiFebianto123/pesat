@include('header')
<div class="container">
    <div class="bs-callout bs-callout-primary">
        <h2>Akun Saya</h2>
    </div>
<div class="accordion" style = "margin-bottom:30px">
  <div class="accordion-item">
      <div class="accordion-body">
        <form action="{{ route('validate') }}" method="post">
        {{ csrf_field() }}
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="exampleInputPassword1">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Remember Me</label>
            </div>
                <button type="submit" class="btn btn btn-primary">Masuk</button>
        </form>
        <label>Lupa password ?</label>
        </br></br>
        <label>Belum Memiliki Akun ? Daftar disini</label>
    </div>
  </div>
</div>

</div>
@include('footer')