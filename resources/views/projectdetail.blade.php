@include('header')
<form id="form-project-detail" action="{{url('project-order/' . $project->project_id)}}" method="post">
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
                    <a href="{{asset('storage/'.$imgDetail->image_detail)}}" target="_blank" style="text-decoration:none;color: inherit;">
                        <img src="{{asset('storage/'.$imgDetail->image_detail)}}" width="200" height="150">
                    </a>
                </div>
                @endforeach
            </div>
            <div class="col-12">
                <a href="{{asset('storage/'.$project->featured_image)}}" target="_blank" style="text-decoration:none;color: inherit;">
                <img src="{{asset('storage/'.$project->featured_image)}}" width="100%" height="700">
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
                {{strip_tags($project->discription);}}
                
            </p>
            </div>
 
        </div>
        
</div>


        <div class="col-6">
            <h2>{{$project->title}}</h2>
            <input type="hidden" name = "projectid" value="{{$project->project_id}}"> 
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
                            <td>Rp. {{number_format($project->amount, 2, ',', '.')}}</td>
                            <td>Rp. {{number_format($project->last_amount, 2, ',', '.')}}</td>
                            @if($project->end_date == null)
                            <td>&#8734;</td>
                            @else
                            <td>{{$interval}}</td>
                            @endif

                        </tr>
                </tbody>
            </table>
            <div>
                @php
                    $progress = 0;
                    if($project->last_amount >= $project->amount){
                        $progress = 100;
                    }
                    else{
                        $progress = round(($project->amount == 0 ? 0 : ($project->last_amount / $project->amount)), 2);
                    }
                @endphp
                <label
                    style="font-family: priva,Arial,sans-serif;
                    font-weight: 300; 
                    color: #686c8b;
                    font-size: 22px;"
                >Raised Percent: {{$progress}}%</label>
            </div>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: {{$progress}}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            </br>
            @if($project->is_closed == false)
            <div class="row">
                <div class="col-8 form-group">
                    <div class="row">
                        <div class="col-7">
                            <div class="wrapper-inline-proyek">
                                <span>Rp.</span>
                                <input type="number" style="width: 120px;" name="donation" class="form-control {{$errors->has('donation') ? 'is-invalid' : ''}}" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary" id="btn-donation">Donation</button>
                        </div>
                    </div>
                    @if ($errors->has('donation'))
                    @foreach ($errors->get('donation') as $message)
                        <div class="invalid-feedback d-block">{{$message}}</div>
                    @endforeach
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</form>
@push('after_scripts')
    <script>
        $('#form-project-detail').submit(function(){
            $('#btn-donation').attr('disabled', true);
        });
    </script>
@endpush
@include('footer')