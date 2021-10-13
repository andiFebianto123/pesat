<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province as ModelsProvince;
use Backpack\NewsCRUD\app\Models\Province;

class ProvinceCustomController extends Controller
{
    public function index(Request $request)
    {
        $search_term = $request->input('q');
       
        if ($search_term)
        {
            $results = ModelsProvince::where('province_name', 'LIKE', '%'.$search_term.'%')->paginate(10);
        
        }
        else
        {   
            $results = ModelsProvince::paginate(10);
            
        }
   
        return $results;
    }
    public function getCity(Request $request){
        
        
        $search_term = $request->input('q');
        $provinceid= $request->input('province_id');

        if ($search_term)
        {
        
            $results = City::where('city_name','like','%'.$search_term.'%')
                            //where('province_id',$provinceid)
                            ->paginate(10);
        
        }
        else
        {   
            $results = City::paginate(10);//where('province_id',$provinceid)->
            
        }
   
        return $results;
        
    }
}