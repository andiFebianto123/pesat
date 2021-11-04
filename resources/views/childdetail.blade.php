@include('header')

<div class="container">
    <br>
<div class="bs-callout bs-callout-primary"><h2>{{$childs->full_name}}</h2></div>
</br>
<div class="row">
<div class="col-4">


        <div class="card" style="width: 23rem; margin-bottom:30px;">
        @if($childs->photo_profile == '')
        <img class="card-img-top" src="{{asset('storage/image/blank.png')}}" alt="Card image cap">
        @else
            <img class="card-img-top" src="{{asset('storage/'.$childs->photo_profile)}}" alt="Card image cap">
        @endif
        </div>
</div>
<div class="col-5">
    <h3>{{$childs->full_name}}</h3>
    <h3>Rp. 150.000,-/ Bulan</h3>
    </br>
    <h5>Monthly Subscription</h5>
        <select class="form-select" aria-label="Default select example">
                <option selected>Choose an option</option>
                <option value="1">1 Bulan</option>
                <option value="3">3 Bulan</option>
                <option value="6">6 Bulan</option>
                <option value="12">12 Bulan</option>
        </select>
    </br>
    <a href="{{url('transaction/')}}" style="text-decoration:none;color: inherit;">
        <button type="submit" class="btn btn-success">Donation</button>
    </a>
    </br>
    </br>
    <p>Tetapkan untuk berkomitmen mensponsori anak minimal 1 tahun</p>
    <hr>
    <p>*) Anda bisa mengirimkan donasi setiap bulan, per 3 bulan, 6 bulan, atau sekaligus untuk 1 tahun.</p>
</div>
</div>
<div class="col-9">
<table class="table table-bordered">
  <tr>
      <td>Jenis Kelamin</td>
      <td>{{$childs->gender}}</td>
  </tr>
  <tr>
      <td>Tempat Lahir</td>
      <td>{{$childs->hometown}}</td>
  </tr>
  <tr>
      <td>Tanggal Lahir</td>
      <td>{{$childs->date_of_birth}}</td>
  </tr>
  <tr>
      <td>FC</td>
      <td>{{$childs->fc}}</td>
  </tr>
  <tr>
      <td>Propinsi</td>
      <td>{{$childs->province_name}}</td>
  </tr>
  <tr>
      <td>Kelas</td>
      <td>{{$childs->class}}</td>
  </tr>
</table>
</div>
</div>

@include('footer')