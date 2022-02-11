@include('header')

<div class="container">
    @if ($donateGood != null)
        <div class="col-12" >
            <div class="bs-callout bs-callout-primary">
                <h2>{{$donateGood->title}}</h2>
            </div>
           {!! $donateGood->discription!!}
        </div>
    @else
    <div class="alert alert-secondary">Informasi untuk sponsor barang belum tersedia.</div>
    @endif
</div>
@include('footer')