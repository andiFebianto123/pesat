@include('header')
<form id="form-child-detail" action="{{url('order')}}" method="post">
{{ csrf_field() }}
<div class="container">
    <br>
<div class="bs-callout bs-callout-primary"><h2>{{$childs->full_name}}</h2></div>
</br>
    @if ($message = Session::get('error'))
        <div class="alert alert-danger" role="alert">
            <strong>{{$message}}</strong>
        </div>
    @endif
    
    @if ($message = Session::get('errorsponsor'))
        <div class="alert alert-danger" role="alert">
            <strong>{{$message}}</strong>
        </div>
    @endif
    @if ($message = Session::get('success'))
        <div class="alert alert-danger" role="alert">
            <strong>{{$message}}</strong>
        </div>
    @endif
    
<div class="row">
<div class="col-4">

        <div class="card" style="width: 23rem; margin-bottom:30px;">
        @if($childs->photo_profile == '')
        <img class="card-img-top" src="{{asset('images/blank.png')}}" alt="Card image cap">
        @else
            <img class="card-img-top" src="{{asset('storage/'.$childs->photo_profile)}}" alt="Card image cap">
        @endif
        </div>
</div>
<div class="col-5">
    <input type="hidden" name="childid" value="{{$childs->child_id}}" />
    <h3>{{$childs->full_name}}</h3>
    <h3>Rp. {{number_format($childs->price, 2, ',', '.')}},-/ Bulan</h3>
    <input type="hidden" name="price" value="{{$childs->price}}" />
    </br>
    <h5>Monthly Subscription</h5>
        <select id= "select-monthly" class="form-select" ria-label="Default select example" name="monthly_subs">
                <option selected>-</option>
                <option value="1">1 Bulan</option>
                <option value="3">3 Bulan</option>
                <option value="6">6 Bulan</option>
                <option value="12">12 Bulan</option>
        </select>
    </br>
    @if($childs->is_sponsored == false)
    <button id="bt-monthly" type="submit" class="btn btn-success" disabled='true'>Donation</button>
    @endif
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

</form>

@include('footer')