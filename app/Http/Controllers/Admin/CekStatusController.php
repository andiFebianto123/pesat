<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\DataOrder;
use App\Models\ChildMaster;
use App\Models\OrderProject;
use App\Models\ProjectMaster;
use App\Models\DataDetailOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\HistoryStatusPayment;
use App\Models\ProjectHistoryStatusPayment;

class CekStatusController extends Controller
{

    public function index($id)
    {
        DB::beginTransaction();
        try {

            $orderProject = OrderProject::where('order_project_id', $id)->first();

            if (empty($orderProject)) {
                DB::rollback();
                \Alert::add('error', 'Order proyek yang dimaksud tidak ditemukan.')->flash();
                return redirect(backpack_url('data-order-project'));
            }

            try {
                $orderId = $orderProject->order_project_id_midtrans;
                $decoderespon = \Midtrans\Transaction::status($orderId);

                $response = json_encode($decoderespon);
                $statuscode = $decoderespon->status_code;
            } catch (Exception $e) {
                DB::rollback();

                if ($e->getCode() == 404) {
                    $message = 'Order proyek belum terdaftar di Midtrans.';
                } else {
                    $message = 'Gagal mendapatkan status order proyek dari Midtrans. [' . $e->getCode() . ']';
                }

                \Alert::error($message)->flash();
                return redirect(backpack_url('data-order-project'));
            }

            $transaction = $decoderespon->transaction_status;
            $type = $decoderespon->payment_type;
            $order_id = $decoderespon->order_id;

            $paymentStatus = null;

            if ($transaction  == 'capture' || $transaction == 'settlement') {
                $paymentStatus = 2;
            } else if ($transaction == 'pending' || $transaction == 'expire') {
                if ($transaction == 'expire') {
                    $paymentStatus = $orderProject->payment_status;
                } else {
                    $paymentStatus = 1;
                }
            } else if ($transaction == 'deny' || $transaction == 'cancel') {
                $paymentStatus = 3;
            } else {
                DB::rollback();
                \Alert::error('Status Midtrans : ' . $transaction . ' tidak dapat diproses sistem.')->flash();
                return redirect(backpack_url('data-order-project'));
            }

            $orderProject->status_midtrans = $transaction;
            $orderProject->payment_status = $paymentStatus;
            $orderProject->payment_type = $type;
            $orderProject->save();

            ProjectHistoryStatusPayment::create([
                'detail_history' => $response,
                'status' => $paymentStatus,
                'user_id' => backpack_user()->id,
                'status_midtrans' => $transaction,
            ]);


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

            DB::commit();

            \Alert::success('Status pembayaran berhasil diperbarui.')->flash();
            return redirect(backpack_url('data-order-project'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function childcekstatus($id)
    {
        DB::beginTransaction();

        try {

            $getStatus = DataOrder::where('order_id', $id)->first();
            if ($getStatus == null) {
                DB::rollback();
                \Alert::add('error', 'Order anak yang dimaksud tidak ditemukan.')->flash();
                return redirect(backpack_url('data-order'));
            }

            $getStatusMidtrans = $getStatus->order_id_midtrans;
            $resetOrder = false;

            try {
                $decoderespon = \Midtrans\Transaction::status($getStatusMidtrans);
                $transaction = $decoderespon->transaction_status;
            } catch (Exception $e) {
                DB::rollBack();
                if ($e->getCode() == '404') {
                    \Alert::add('error', 'Order anak belum terdaftar di Midtrans.')->flash();
                    return redirect(backpack_url('data-order'));
                } else if ($e->getCode() != '404') {
                    \Alert::add('error', "Gagal mendapatkan status order anak dari Midtrans. ["  . $e->getCode() . "]")->flash();
                    return redirect(backpack_url('data-order'));
                }
            }

            $response = json_encode($decoderespon);

            $transaction = $decoderespon->transaction_status;
            $type = $decoderespon->payment_type;
            $order_id = $decoderespon->order_id;

            if ($transaction == 'capture') {
                $status = 2;
            } else if ($transaction == 'settlement') {
                $status = 2;
            } else if ($transaction == 'pending') {
                $status = 1;
            } else if ($transaction == 'deny') {
                $status = 3;
                $resetOrder = true;
            } else if ($transaction == 'expire') {
                $status = $getStatus->payment_status;
            } else if ($transaction == 'cancel') {
                $status = 3;
                $resetOrder = true;
            } else {
                DB::rollBack();
                \Alert::add('error', 'Status Midtrans : ' . $transaction . ' tidak dapat diproses sistem.')->flash();
                return redirect(backpack_url('data-order'));
            }

            $getStatus->status_midtrans = $transaction;
            $getStatus->payment_status = $status;
            $getStatus->payment_type = $type;
            $getStatus->save();

            HistoryStatusPayment::create([
                'detail_history' => $response,
                'status' => $status,
                'status_midtrans' => $transaction,
                'user_id' => backpack_user()->id,
            ]);
            
            DB::commit();

            \Alert::add('success', 'Status pembayaran berhasil di perbarui.')->flash();
            return redirect(backpack_url('data-order'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
