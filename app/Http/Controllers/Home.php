<?php

namespace App\Http\Controllers;

use App\Models\ChildMaster;
use App\Models\Province;
use Illuminate\Http\Request;

class Home extends Controller
{
    public function index(Request $request){
        
        $data['childs']     = ChildMaster::where('child_master.deleted_at',null)
                              ->paginate(9);
        $data['provinces']  = Province::where('province.deleted_at',null)
                              ->get();
        $data['class']      = ChildMaster::where('child_master.deleted_at',null)
                              ->get()
                              ->groupBy('class');

        return view('Home',$data);
    }


}
