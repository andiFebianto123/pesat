<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use App\Models\DataOrder;
use App\Models\OrderProject;
use App\Models\ProjectMaster;
use Illuminate\Http\Request;

class CancelOrderController extends Controller
{
    //
    public function index($id)
    {
        $cekDatas = DataOrder::where('order_id',$id)->first();

        $cekDetailOrder = DataDetailOrder::where('order_id',$id)->get('child_id');

        if($cekDatas->payment_status == 2){
      
            \Alert::add('error', 'Tidak bisa cancel order, karena sudah ada pembayaran')->flash();
            return back()->withMessage(['message' => 'Tidak bisa cancel order, karena sudah ada pembayaran']);
      
        }else{
            
            $orderHd                 = DataOrder::find($id);
            $orderHd->payment_status = 3;
            $orderHd->save();

            foreach($cekDetailOrder as $key => $detailOrder)
            {
                
                $child = ChildMaster::find($detailOrder->child_id);
                $child->is_sponsored = 0;
                $child->current_order_id = null;
                $child->save();
                //dd($detailOrder->child_id);
                
            }

            \Alert::add('success', 'Transaksi berhasil dibatalkan')->flash();
            return back()->withMessage(['message' => 'Transaksi berhasil dibatalkan']);
        
        }

        
    }
    public function projectcancelorder($id)
    {

        $projectOrder = OrderProject::where('order_project_id',$id)->first();

        if($projectOrder->payment_status == 2){
           
            \Alert::add('error', 'Tidak bisa cancel order, karena sudah ada pembayaran')->flash();
            return back()->withMessage(['message' => 'Tidak bisa cancel order, karena sudah ada pembayaran']);

        }else{

                $projectOrder->payment_status = 3;
                $projectOrder->save();

                \Alert::add('success', 'Order berhasil dibatalkan')->flash();
                return back()->withMessage(['message' => 'Order berhasil dibatalkan']);
        }

    }
}
