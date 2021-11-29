
@include('header')
    <div class="container">
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
            <div class="row">
                <div class="col-10">
                </div>
                <div class="col-2">
                    <button type="submit" class="btn btn-primary" style="margin-top:10px;margin-left:80px">Apply Filter</button>
                </div>
           
            </div>
        </div>
    </form>
       </br>
       
        <div class="row">
            @foreach ($projects as $key => $project)
    
            <div class="col-4">
                <a href="{{url('project-detail/'.$project->project_id)}}" style="text-decoration:none;color: inherit;">
                    <input type="hidden" name="projectid" value="{{$project->project_id}}">
                        <div class="card" style="width: 23rem;margin-left:25px; margin-bottom:30px;">
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