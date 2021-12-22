<?php

namespace App\Http\Controllers;

use App\Models\ProjectMaster;
use App\Models\ProjectMasterDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ListProyekController extends Controller
{
    //
    public function index(Request $request){

        $text = $request->search;

        if($text == null){

            $project = ProjectMaster::paginate(9);
            
    
        }else{
            $project= ProjectMaster::where('title','like','%'.$text.'%')
                                    ->paginate(9);
        }
        $data['projects'] = $project;
        return view('listproyek',$data);

    }

    public function projectdetail($id){

        $project = ProjectMaster::where('project_id',$id)->first();

        if(empty($project)){
            return redirect()->back()->with(['error' => 'Data Proyek Yang Dimaksud Tidak Ditemukan.']);
        }else{

        
        $imgDetail = ProjectMasterDetail::where('project_id',$id)->get();

        $now = Carbon::now();
    
     //   $startdate = Carbon::parse($project->start_date);
        $enddate   = Carbon::parse($project->end_date);
        $interval  = $enddate->diffInDays($now);
    
        $data['imgDetails'] = $imgDetail;
        $data['projects']   = $project;
        if($now >= $enddate){
        $data['interval']   = 0;
        }else{
        $data['interval']   = $interval;
        }
        return view('projectdetail',$data);
    }
    }
}
