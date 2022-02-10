<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Config;
use App\Models\DataOrder;
use App\Models\ChildMaster;
use App\Models\OrderProject;
use App\Mail\PaymentComplete;
use App\Models\ProjectMaster;
use App\Models\DataDetailOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\HistoryStatusPayment;
use Illuminate\Support\Facades\Mail;
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
            $type = ucwords(str_replace('_', ' ', $decoderespon->payment_type));
            if($type == 'Bank Transfer'){
                $type .= ' - ' . (strtoupper($decoderespon->va_numbers[0]->bank ?? ''));
            }
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
            $type = ucwords(str_replace('_', ' ', $decoderespon->payment_type));
            if($type == 'Bank Transfer'){
                $type .= ' - ' . (strtoupper($decoderespon->va_numbers[0]->bank ?? ''));
            }
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

                $order = DataOrder::where('order_id', $order_id)->with('sponsorname')->first();
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

                }
            }
            DB::commit();
            try{
                if($paymentStatus == 2){
                    $orderDetails = DataDetailOrder::where('order_id', $order->order_id)->with('childname')->get();
                    // SPONSOR
                    Mail::to($order->sponsorname->email)
                    ->send(new PaymentComplete($order, $orderDetails, 
                    'Terima kasih atas donasi Anda di Pesat #' . $order->order_id, 
                    'Terima kasih, Donasi #' . $order->order_id));

                    // ADMIN
                    $config = Config::where('key', 'Administration Email Address')->first();
                    if($config != null){
                        $explodedEmail = collect(explode(',', $config->value));
                        $email = $explodedEmail->shift();
                        $cc = $explodedEmail->toArray();
                        $mail =  Mail::to($email);
                        if(count($cc) != 0){
                            $mail->cc($cc);
                        }
                        $mail   ->send(new PaymentComplete($order, $orderDetails, 
                        'Donasi Baru di Pesat #' . $order->order_id, 
                        'Detail Donasi #' . $order->order_id));
                    }
                }
            }
            catch(Exception $e){
                Log::channel('notificationmidtrans')->info('Email order anak ID Midtrans : ' . $order_id_midtrans);
                Log::channel('notificationmidtrans')->error($e);
            }
        } catch (Exception $e) {
            DB::rollback();
            Log::channel('notificationmidtrans')->info('Order anak ID Midtrans : ' . $order_id_midtrans);
            Log::channel('notificationmidtrans')->error($e);
        }
    }

}
