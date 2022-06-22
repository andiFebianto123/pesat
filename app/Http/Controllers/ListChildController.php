<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Province;
use App\Models\DataOrder;
//use Illuminate\Contracts\Session\Session;
use App\Models\ChildMaster;
use Illuminate\Http\Request;
use App\Models\DataDetailOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ListChildController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $provinceid = $request->input('provinceid');
        $class = $request->input('class');
        $gender = $request->input('gender');
        $childsdatas = ChildMaster::where('deleted_at', null)
        ->orderBy(DB::raw('IF(is_sponsored = 1 OR EXISTS(SELECT 1 FROM order_dt WHERE order_dt.child_id = child_master.child_id AND order_dt.deleted_at IS NULL 
        AND start_order_date <= "' . $now->format('Y-m-d') . '" AND end_order_date >= "' . $now->format('Y-m-d') . '"
        AND EXISTS(SELECT 1 FROM order_hd WHERE order_hd.order_id = order_dt.order_id AND order_hd.deleted_at IS NULL AND order_hd.payment_status <= 2)), 0, 1)'), 'desc')
        ->orderBy(DB::raw('IF(photo_profile IS NULL, 0, 1)'), 'desc')
        ->orderBy('child_id', 'desc');

        if ($provinceid == null && $class == null &&  $gender == null) {

            $childs = $childsdatas->paginate(9);
        } else {

            if (isset($provinceid)) {

                $childs = $childsdatas->where('province_id', $provinceid)
                    ->paginate(9);
            }
            if ($gender == 'L') {
                $childs = $childsdatas->where('gender', '=', 'L')
                    ->paginate(9);
            }
            if ($gender == 'P') {
                $childs = $childsdatas->where('gender', '=', 'P')
                    ->paginate(9);
            }
            if (isset($class)) {
                $childs = $childsdatas->where('class', $class)
                    ->paginate(9);
            }
        }

        $now = Carbon::now()->startOfDay();

        $childsData = $childs->map(function ($child, $key) use ($now) {
            $child->is_sponsored = $child->is_sponsored || ChildMaster::getStatusSponsor($child->child_id, $now);
            return $child;
        });

        // $childsData->all();


        $data['provinces']  = Province::where('province.deleted_at', null)
            ->get();
        $data['class']      = ChildMaster::where('child_master.deleted_at', null)
            ->groupBy('class')->select('class')->get()->pluck('class');

        $data['childs'] = $childs;
        $data['title'] = 'Sponsor Anak';

        return view('listchild', $data);
    }
    public function childdetail($id)
    {
        $getChild = ChildMaster::where('child_id', $id)->first();
        if (empty($getChild)) {
            return redirect(url('list-child'))->with(['error' => 'Anak yang dimaksud tidak ditemukan.']);
        } else {
            $now = Carbon::now()->startOfDay();

            $childdata      = ChildMaster::where('child_id', $id)
                ->join('city as c1', 'c1.city_id', 'child_master.hometown')
                ->join('city as c2', 'c2.city_id', 'child_master.city_id')
                ->join('province as p', 'p.province_id', 'child_master.province_id')
                ->addSelect('c1.city_name as hometown')
                ->addSelect('c2.city_name as city')
                ->addSelect('p.province_name')
                ->addSelect('child_master.child_id')
                ->addSelect('child_master.full_name')
                ->addSelect('child_master.is_sponsored')
                ->addSelect('child_master.price')
                ->addSelect('child_master.photo_profile')
                ->addSelect('child_master.fc')
                ->addSelect('child_master.date_of_birth')
                ->addSelect('child_master.class')
                ->addSelect('child_master.gender')
                ->first();

            $childdata->is_sponsored =  $childdata->is_sponsored || ChildMaster::getStatusSponsor($getChild->child_id, $now);
            $data['childs'] = $childdata;

            $data['title'] = 'Detail Anak';

            return view('childdetail', $data);
        }
    }
}
