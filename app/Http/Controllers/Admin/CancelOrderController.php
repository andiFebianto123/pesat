<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use App\Models\DataOrder;
use App\Models\OrderProject;
use App\Models\ProjectMaster;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class CancelOrderController extends Controller
{
    //
    public function index($id)
    {
        $cekDatas = DataOrder::where('order_id', $id)->first();

        if (empty($cekDatas)) {
            \Alert::add('error', 'Order anak tidak ditemukan.')->flash();
            return redirect(backpack_url('data-order'));
        }

        $getOrderIdMidtrans = $cekDatas->order_id_midtrans;

        $cekDetailOrder = DataDetailOrder::where('order_id', $id)->get('child_id');
        DB::beginTransaction();

        try {

            //$orderId = $id . "-anak";
            \Midtrans\Transaction::cancel($getOrderIdMidtrans);

            $orderHd = DataOrder::find($id);
            $orderHd->payment_status = 3;
            $orderHd->status_midtrans = 'cancel';
            $orderHd->save();

            foreach ($cekDetailOrder as $key => $detailOrder) {

                $child = ChildMaster::find($detailOrder->child_id);
                $child->is_sponsored = 0;
                $child->is_paid = 0;
                if ($child->current_order_id == $id) {
                    $child->current_order_id = null;
                }
                $child->save();
            }

            DB::commit();

            \Alert::add('success', 'Order anak berhasil dibatalkan')->flash();
            return redirect(backpack_url('data-order'));
        } catch (Exception $e) {

            if ($e->getCode() == 404) {

                $orderHd = DataOrder::find($id);
                $orderHd->payment_status = 3;
                $orderHd->save();

                foreach ($cekDetailOrder as $key => $detailOrder) {

                    $child = ChildMaster::find($detailOrder->child_id);
                    $child->is_sponsored = 0;
                    $child->current_order_id = null;
                    $child->save();
                }

                DB::commit();

                \Alert::add('success', 'Order berhasil dibatalkan')->flash();
                return redirect(backpack_url('data-order'));
            } elseif ($e->getCode() == 412) {

                DB::commit();
                \Alert::add('error', 'Status transaksi sudah tidak bisa dirubah')->flash();
                return redirect(backpack_url('data-order'));
            } else {
                DB::rollBack();
                \Alert::add('error', "Gagal melakukan perubahan status order anak di Midtrans. [" . $e->getCode() . "]")->flash();
                return redirect(backpack_url('data-order'));
            }
        }
    }
    public function projectcancelorder($id)
    {

        DB::beginTransaction();
        try {
            $projectOrder = OrderProject::where('order_project_id', $id)->first();

            if (empty($projectOrder)) {
                DB::rollback();
                \Alert::add('error', 'Order proyek yang dimaksud tidak ditemukan.')->flash();
                return redirect(backpack_url('data-order-project'));
            }

            try {
                $orderId = $projectOrder->order_project_id_midtrans;
                \Midtrans\Transaction::cancel($orderId);
            } catch (Exception $e) {
                if ($e->getCode() != 404) {
                    DB::rollBack();
                    \Alert::add('error', 'Gagal melakukan perubahan status order proyek di Midtrans. [' . $e->getCode() . ']')->flash();
                    return redirect(backpack_url('data-order-project'));
                }
            }

            $projectOrder->payment_status = 3;
            $projectOrder->status_midtrans = 'cancel';
            $projectOrder->save();

            $getProjectId = $projectOrder->project_id;
            $totalPrice = OrderProject::where('project_id', $getProjectId)
                ->where('payment_status', 2)
                ->groupBy('project_id')
                ->sharedLock()
                ->sum('price');

            $project = ProjectMaster::where('project_id', $getProjectId)->first();

            $amount = $project->amount;
            $enddate = null;
            if ($project->end_date != null) {
                $enddate = Carbon::parse($project->end_date)->startOfDay();
            }
            $now = Carbon::now()->startOfDay();

            $project->last_amount = $totalPrice;
            $project->is_closed = ($enddate != null && $now > $enddate) || $totalPrice >= $amount;
            $project->save();

            DB::commit();

            \Alert::add('success', 'Order proyek berhasil dibatalkan.')->flash();
            return redirect(backpack_url('data-order-project'));
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
