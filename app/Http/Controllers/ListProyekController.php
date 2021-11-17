<?php

namespace App\Http\Controllers;

use App\Models\ProjectMaster;
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
        return view('projectdetail');
    }
}
