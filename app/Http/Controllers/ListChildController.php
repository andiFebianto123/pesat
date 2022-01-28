<?php

namespace App\Http\Controllers;

use App\Models\ChildMaster;
use App\Models\DataOrder;
use App\Models\DataDetailOrder;
use App\Models\Province;
//use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Exception;

class ListChildController extends Controller
{
    public function index(Request $request)
    {
        $provinceid = $request->input('provinceid');
        $class = $request->input('class');
        $gender = $request->input('gender');
        $childsdatas = ChildMaster::where('deleted_at', null);

        if ($provinceid == null && $class == null &&  $gender == null) {

            $childs = $childsdatas->paginate(9);
        } else {

            if (isset($provinceid)) {

                $childs = $childsdatas->where('province_id', $provinceid)
                    ->paginate(9);
            }
            if ($gender == 1) {
                $childs = $childsdatas->where('gender', 'LIKE', "%laki%")
                    ->paginate(9);
            }
            if ($gender == 2) {
                $childs = $childsdatas->where('gender', 'LIKE', "%perem%")
                    ->paginate(9);
            }
            if (isset($class)) {
                $childs = $childsdatas->where('class', $class)
                    ->paginate(9);
            }
        }


        $childsData = $childs->map(function ($child, $key) {
            $isSponsored = false;
            $now = Carbon::now()->startOfDay();

            $cekStatusPayment = DataDetailOrder::where('child_id', $child->child_id)
                ->whereDate('start_order_date', '<=', $now)
                ->whereDate('end_order_date', '>', $now)
                ->join('order_hd', 'order_hd.order_id', 'order_dt.order_id')
                ->addSelect('order_hd.order_id_midtrans')
                ->first();

            if ($cekStatusPayment != null) {
                $getStatusMidtrans = $cekStatusPayment->order_id_midtrans;
                $transaction = null;
                try {
                    $decoderespon = \Midtrans\Transaction::status($getStatusMidtrans);
                    $transaction = $decoderespon->transaction_status;
                } catch (Exception $e) {
                }

                if ($transaction == 'capture') {
                    $isSponsored = true;
                } else if ($transaction == 'settlement') {
                    $isSponsored = true;
                } else if ($transaction == 'pending') {
                    $isSponsored = true;
                }
            }

            $child->is_sponsored = $isSponsored;
            return $child;
        });

        $childsData->all();


        $data['provinces']  = Province::where('province.deleted_at', null)
            ->get();
        $data['class']      = ChildMaster::where('child_master.deleted_at', null)
            ->get()
            ->groupBy('class');

        $data['childs'] = $childs;

        return view('listchild', $data);
    }
    public function childdetail($id)
    {
        $getChild = ChildMaster::where('child_id', $id)->first();
        if (empty($getChild)) {
            return redirect(url('list-child'))->with(['error' => 'Anak yang dimaksud tidak ditemukan.']);
        } else {
            $isSponsored = false;
            $now = Carbon::now()->startOfDay();

            $cekStatusPayment = DataDetailOrder::where('child_id', $getChild->child_id)
                ->whereDate('start_order_date', '<=', $now)
                ->whereDate('end_order_date', '>', $now)
                ->join('order_hd', 'order_hd.order_id', 'order_dt.order_id')
                ->addSelect('order_hd.order_id_midtrans')
                ->first();

            if ($cekStatusPayment != null) {
                $getStatusMidtrans = $cekStatusPayment->order_id_midtrans;
                $transaction = null;
                try {
                    $decoderespon = \Midtrans\Transaction::status($getStatusMidtrans);
                    $transaction = $decoderespon->transaction_status;
                } catch (Exception $e) {
                }

                if ($transaction == 'capture') {
                    $isSponsored = true;
                } else if ($transaction == 'settlement') {
                    $isSponsored = true;
                } else if ($transaction == 'pending') {
                    $isSponsored = true;
                }
            }

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

            $childdata->is_sponsored = $isSponsored;
            $data['childs'] = $childdata;

            return view('childdetail', $data);
        }
    }
}
