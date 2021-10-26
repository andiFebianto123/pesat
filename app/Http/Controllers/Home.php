<?php

namespace App\Http\Controllers;

use App\Models\ChildMaster;
use Illuminate\Http\Request;

class Home extends Controller
{
    public function index(){
        
        $data['childs']= ChildMaster::where('child_master.deleted_at',null)
       // ->get()
        ->paginate(3);
        //$data=ChildMaster::
        return view('Home',$data);
    }

}
