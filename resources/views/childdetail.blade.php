@include('header')
<form id="form-child-detail" action="{{ url('order') . '/' . $childs->child_id }}">
    <div class="container pb-5 pb-md-0">
        <br>
        <div class="bs-callout bs-callout-primary">
            <h2>{{ $childs->full_name }}</h2>
        </div>
        </br>
        @if ($message = Session::get('error'))
            <div class="alert alert-danger" role="alert">
                <strong>{{ $message }}</strong>
            </div>
        @endif

        @if ($message = Session::get('errorsponsor'))
            <div class="alert alert-danger" role="alert">
                <strong>{{ $message }}</strong>
            </div>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-danger" role="alert">
                <strong>{{ $message }}</strong>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-4">

                <div class="card">
                    @if ($childs->photo_profile == '')
                        <img class="card-img-top" src="{{ asset('images/blank.png') }}" alt="Card image cap">
                    @else
                        <img class="card-img-top" src="{{ asset('storage/' . $childs->photo_profile) }}"
                            alt="Card image cap">
                    @endif
                </div>
            </div>
            <div class="col-lg-8 my-4 my-lg-0">
                <h3>{{ $childs->full_name }}</h3>
                <h3>Rp. {{ number_format($childs->price, 2, ',', '.') }},-/ Bulan</h3>
                @if ($childs->is_sponsored == false)
                    </br>
                    <h5>Monthly Subscription</h5>
                    <select id="select-monthly"
                        class="form-select form-control w-50 {{ $errors->has('monthly_subscription') ? 'is-invalid' : '' }}"
                        name="monthly_subscription">
                        <option selected>-</option>
                        <option value="1">1 Bulan</option>
                        <option value="3">3 Bulan</option>
                        <option value="6">6 Bulan</option>
                        <option value="12">12 Bulan</option>
                    </select>
                    @if ($errors->has('monthly_subscription'))
                        @foreach ($errors->get('monthly_subscription') as $message)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @endforeach
                    @endif
                    </br>
                    <button id="bt-monthly" type="submit" class="btn btn-success" disabled='true'>Donation</button>
                    </br>
                @else
                    <p class="mb-0">Status : <span class="text-danger">Tersponsori</span></p>
                @endif
                </br>
                </br>
                <hr>
                <small>*) Anda bisa mengirimkan donasi setiap bulan, per 3 bulan, 6 bulan, atau sekaligus untuk 1
                    tahun.</small>
            </div>
        </div>
        <div class="col-lg-9 mt-lg-4">
            <table class="table table-bordered">
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>{{ $childs->gender }}</td>
                </tr>
                <tr>
                    <td>Tempat Lahir</td>
                    <td>{{ $childs->hometown }}</td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>{{ $childs->date_of_birth }}</td>
                </tr>
                <tr>
                    <td>FC</td>
                    <td>{{ $childs->fc }}</td>
                </tr>
                <tr>
                    <td>Propinsi</td>
                    <td>{{ $childs->province_name }}</td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>{{ $childs->class }}</td>
                </tr>
            </table>
        </div>
    </div>

</form>

@include('footer')
