<?php

namespace App\Http\Controllers;

use App\Models\ChildMaster;
use App\Models\Province;
use Illuminate\Http\Request;

class Home extends Controller
{
    public function index(Request $request){
        
        $provinceid = $request->input('provinceid');
        $class = $request->input('class');
        $gender = $request->input('gender');
        $childsdatas = ChildMaster::where('deleted_at',null);
      
        if($provinceid==null && $class==null &&  $gender == null ){

        $child = $childsdatas->paginate(9);
        }else{

        if (isset($provinceid)) {
           
            $child = $childsdatas->where('province_id',$provinceid)
                                ->paginate(9);
        }
        if($gender==1){
            $child = $childsdatas->where('gender','LIKE',"%laki%")
                                 ->paginate(9);
            
        }
        if($gender==2){
            $child = $childsdatas->where('gender','LIKE',"%perem%")
                                 ->paginate(9);
        }
        if(isset($class)){
            $child= $childsdatas->where('class',$class)
                                ->paginate(9);
        }

    }
        
        $data['childs'] = $child;

        $data['provinces']  = Province::where('province.deleted_at',null)
                              ->get();
        $data['class']      = ChildMaster::where('child_master.deleted_at',null)
                              ->get()
                              ->groupBy('class');

        return view('Home',$data);
    }
    // public function childdetail($id){

    //     //$id = $request->input('childid');
    //     dd($id);
    //     return view('childdetail');

    // }


}
