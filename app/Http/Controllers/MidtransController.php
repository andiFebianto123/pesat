<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\DataOrder;
use App\Models\ChildMaster;
use App\Models\OrderProject;
use App\Models\ProjectMaster;
use App\Models\DataDetailOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\HistoryStatusPayment;
use App\Models\ProjectHistoryStatusPayment;

class MidtransController extends Controller
{
    //
    public function notification()
    {
        try {
            $notif = new \Midtrans\Notification();
        } catch (Exception $e) {
            Log::channel('notificationmidtrans')->error($e);
            return;
        }

        $order_id = $notif->order_id;

        $explodeId = explode('-', $order_id);

        $cekType = $explodeId[0] ?? '';

        if ($cekType == 'proyek') {
            $this->handleStatusProyek($notif);
        } else if ($cekType == 'anak') {
            $this->handleStatusAnak($notif);
        } else {
            Log::channel('notificationmidtrans')->info(json_encode($notif->getResponse()));
        }

        return response()->json(['message' => 'Success.']);
    }

    private function handleStatusProyek($decoderespon)
    {
        DB::beginTransaction();
        try {
            $response = json_encode($decoderespon->getResponse());
            $transaction = $decoderespon->transaction_status;
            $type = $decoderespon->payment_type;
            $order_id_midtrans = $decoderespon->order_id;

            $explodeId = explode('-', $order_id_midtrans);

            $order_id = $explodeId[1] ?? '';

            $paymentStatus = null;

            if ($transaction == 'capture' || $transaction == 'settlement') {
                $paymentStatus = 2;
            } else if ($transaction == 'pending' || $transaction == 'expire') {
                $paymentStatus = 1;
            } else if ($transaction == 'deny' || $transaction == 'cancel') {
                $paymentStatus = 3;
            } else {
                Log::channel('notificationmidtrans')->info('Order proyek ID Midtrans : ' . $order_id_midtrans);
                Log::channel('notificationmidtrans')->error('Status Midtrans : ' . $transaction . ' tidak dapat diproses sistem.');
            }

            if ($paymentStatus != null) {
                ProjectHistoryStatusPayment::create([
                    'detail_history' => $response,
                    'status' => $paymentStatus,
                    'user_id' => null,
                    'status_midtrans' => $transaction,
                ]);

                $orderProject = OrderProject::where('order_project_id', $order_id)->first();
                if ($orderProject == null) {
                    Log::channel('notificationmidtrans')->info('Order proyek ID Midtrans : ' . $order_id_midtrans);
                    Log::channel('notificationmidtrans')->error('Order proyek yang dimaksud tidak ditemukan.');
                } else if ($orderProject->order_project_id_midtrans != $order_id_midtrans) {
                    Log::channel('notificationmidtrans')->info('Order proyek ID Midtrans : ' . $order_id_midtrans);
                    Log::channel('notificationmidtrans')->error('Order proyek ID Midtrans tidak sama dengan database (' . $orderProject->order_project_id_midtrans . ').');
                } else {
                    $orderProject->status_midtrans = $transaction;
                    $orderProject->payment_status = $paymentStatus;
                    $orderProject->payment_type = $type;
                    $orderProject->save();

                    $getProjectId = $orderProject->project_id;
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
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::channel('notificationmidtrans')->info('Order proyek ID Midtrans : ' . $order_id_midtrans);
            Log::channel('notificationmidtrans')->error($e);
        }
    }

    private function handleStatusAnak($decoderespon)
    {
        DB::beginTransaction();
        try {
            $response = json_encode($decoderespon->getResponse());
            $transaction = $decoderespon->transaction_status;
            $type = $decoderespon->payment_type;
            $order_id_midtrans = $decoderespon->order_id;

            $explodeId = explode('-', $order_id_midtrans);

            $order_id = $explodeId[1] ?? '';

            $paymentStatus = null;

            if ($transaction == 'capture' || $transaction == 'settlement') {
                $paymentStatus = 2;
            } else if ($transaction == 'pending' || $transaction == 'expire') {
                $paymentStatus = 1;
            } else if ($transaction == 'deny' || $transaction == 'cancel') {
                $paymentStatus = 3;
            } else {
                Log::channel('notificationmidtrans')->info('Order anak ID Midtrans : ' . $order_id_midtrans);
                Log::channel('notificationmidtrans')->error('Status Midtrans : ' . $transaction . ' tidak dapat diproses sistem.');
            }

            if ($paymentStatus != null) {
                HistoryStatusPayment::create([
                    'detail_history' => $response,
                    'status' => $paymentStatus,
                    'user_id' => null,
                    'status_midtrans' => $transaction,
                ]);

                $order = DataOrder::where('order_id', $order_id)->first();
                if ($order == null) {
                    Log::channel('notificationmidtrans')->info('Order anak ID Midtrans : ' . $order_id_midtrans);
                    Log::channel('notificationmidtrans')->error('Order anak yang dimaksud tidak ditemukan.');
                } else if ($order->order_id_midtrans != $order_id_midtrans) {
                    Log::channel('notificationmidtrans')->info('Order anak ID Midtrans : ' . $order_id_midtrans);
                    Log::channel('notificationmidtrans')->error('Order anak ID Midtrans tidak sama dengan database (' . $order->order_id_midtrans . ').');
                } else {
                    $order->status_midtrans = $transaction;
                    $order->payment_status = $paymentStatus;
                    $order->payment_type = $type;
                    $order->save();

                    $getOrderId = $order->order_id;

                    $detailOrders = DataDetailOrder::where('order_id', $getOrderId)->get();
                    foreach ($detailOrders as $key => $detailOrder) {
                        $child = ChildMaster::find($detailOrder->child_id);
                        if ($child != null && $child->current_order_id == $getOrderId) {
                            if($paymentStatus == 3){
                                $child->is_sponsored = 0;
                                $child->current_order_id = null;
                                $child->is_paid = 0;
                                $child->save();
                            }
                            else if($paymentStatus == 2){
                                $child->is_sponsored = 1;
                                $child->is_paid = 1;
                                $child->save();
                            }
                            else if($paymentStatus == 1){
                                $child->is_sponsored = 1;
                                $child->is_paid = 0;
                                $child->save();
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::channel('notificationmidtrans')->info('Order anak ID Midtrans : ' . $order_id_midtrans);
            Log::channel('notificationmidtrans')->error($e);
        }
    }

}
