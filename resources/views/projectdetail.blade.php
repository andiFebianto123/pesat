@include('header')
<form id="form-project-detail" action="{{url('project-order')}}" method="post">
{{ csrf_field() }}
</br></br>

<div class="container">
    @if ($message = Session::get('error'))
        <div class="alert alert-danger" role="alert">
            <strong>{{$message}}</strong>
        </div>
    @endif
    <div class="row">
        <div class="col-6">
            <div class="row">
                @foreach ($imgDetails as $key => $imgDetail)
                <div class="col-4" style="margin-bottom: 10px;">
                    <a href="#" style="text-decoration:none;color: inherit;">
                        <img src="{{asset('storage/'.$imgDetail->image_detail)}}" width="200" height="150">
                    </a>
                </div>
                @endforeach
            </div>
            <div class="col-12">
                <a href="#" style="text-decoration:none;color: inherit;">
                <img src="{{asset('storage/'.$projects->featured_image)}}" width="100%" height="700">
                </a>
            </div>
            </br>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Campaign Story</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            </br>
          <h2>Campaign Story</h2>
            </br>
            <p>
                {{strip_tags($projects->discription);}}
                
            </p>
            </div>
 
        </div>
        
</div>


        <div class="col-6">
            <h2>{{$projects->title}}</h2>
            <input type="hidden" name = "projectid" value="{{$projects->project_id}}"> 
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <td>Funding Goal</td>
                        <td>Funds Raised</td>
                        <td>Day to Go</td>
                    </tr>
    
                </thead>
                <tbody>
                        <tr>
                            <td>Rp. {{number_format($projects->amount, 2, ',', '.')}}</td>
                            <td>Rp. {{number_format($projects->last_amount, 2, ',', '.')}}</td>
                            @if($projects->end_date == null)
                            <td>&#8734;</td>
                            @else
                            <td>{{$interval}}</td>
                            @endif

                        </tr>
                </tbody>
            </table>
            </br>
            @if($projects->is_closed == false)
            <div class="row">
                <div class='col-8'>
                    <input type="number" name="total" class="form-control" required>
                </div>
                <div class='col-4'>
                <button type="submit" class="btn btn-primary" style="margin-left:80px">Donation</button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</form>
@include('footer')