<?php

namespace App\Http\Controllers;

use App\Models\ProjectMaster;
use App\Models\ProjectMasterDetail;
use Illuminate\Http\Request;

class ListProyekController extends Controller
{
    //
    public function index(){
        $project = ProjectMaster::all();
        $data['projects'] = $project;
        return view('listproyek',$data);
    }

    public function projectdetail($id){

        $project = ProjectMaster::where('project_id',$id)->first();

        $imgDetail = ProjectMasterDetail::where('project_id',$id)->get();
        $data['imgDetails'] = $imgDetail;
        $data['projects'] = $project;
        
        return view('projectdetail',$data);
    }
}
