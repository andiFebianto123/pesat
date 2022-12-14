
@include('header')
    <div class="container pb-5 pb-md-0">
        <div class="col-12" >
            <div class="bs-callout bs-callout-primary">
                <h2>Sponsor Proyek</h2>
            </div>
            </br>
            <p>
                Kami mengajak anda untuk mendukung pembangunan sarana-prasarana sekolah demi menunjang pendidikan anak-anak desa, seperti pembangunan
                gedung TK, pengadaan perpustakaan TK, serta permainan indoor dan outdoor TK.
                Terima kasih untuk ikut ambil bagian dalam pelayanan ini
            </p>
        </div>
        @if ($message = Session::get('error'))
            <div class="alert alert-danger" role="alert">
                <strong>{{$message}}</strong>
            </div>
        @endif
        <form id="form-filter" action="{{url('/list-proyek')}}" method="GET" >
        
        {!! csrf_field() !!}
        <div class="card card-body">
            <label>Cari Proyek :</label>
                </br>             
            <div class="row">
                <div class='col-12'>
                    <input type="text" name="search" placeholder="Cari proyek" class="form-control">
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary" style="margin-top:10px">Apply</button>
            </div>
        </div>
    </form>
       </br>
       
        <div class="row">
            @foreach ($projects as $key => $project)
    
            <div class="col-md-6 col-lg-4 mb-4">
                <a href="{{url('project-detail/'.$project->project_id)}}" style="text-decoration:none;color: inherit;">
                    <input type="hidden" name="projectid" value="{{$project->project_id}}">
                        <div class="card h-100">
                            @if($project->featured_image == '')
                            <img class="card-img-top" src="{{asset('storage/image/blank.png')}}" alt="Card image cap">
                            @else
                            <img class="card-img-top" src="{{asset('storage/'.$project->featured_image)}}" alt="Card image cap">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{$project->title}}</h5>
                            </div>
                        </div>
                </a>
            </div>
       
        @endforeach
    
        </div>
    <div class="d-flex justify-content-center">
    {!! $projects->links() !!}
    </div>

</div>

    @include('footer')