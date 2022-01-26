<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use App\Models\DataOrder;
use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CekPaidorNot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:paidornot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $now = Carbon::now()->startOfDay();
            $nowAdd2Days = $now->copy()->addDay(-2);

            $dataOrders = DataOrder::whereHas('orderdetails', function ($query) use ($nowAdd2Days) {
                $query->where('start_order_date', '<', $nowAdd2Days->format('Y-m-d'));
            })->where('payment_status', 1)->get();
            foreach ($dataOrders as $datasOrder) {
                $cancelSuccess = false;
                $updateStatusMidtrans = false;
                try {
                    \Midtrans\Transaction::cancel($datasOrder->order_id_midtrans);
                    $cancelSuccess = true;
                    $updateStatusMidtrans = true;
                } catch (Exception $e) {
                    if ($e->getCode() == 404) {
                        $cancelSuccess = true;
                    }
                    else if($e->getCode() == 412){
                        try{
                            $decoderespon = \Midtrans\Transaction::status($datasOrder->order_id_midtrans);
                            if($decoderespon->transaction_status == 'expire'){
                                $cancelSuccess = true;
                            }
                        }
                        catch(Exception $e){

                        }
                    }
                }

                if ($cancelSuccess) {
                    $datasOrder->payment_status = 3;
                    if($updateStatusMidtrans){
                        $datasOrder->status_midtrans = 'cancel';
                    }
                    $datasOrder->save();
                    $cekDetailOrder = DataDetailOrder::where('order_id', $datasOrder->order_id)->get();
                    foreach ($cekDetailOrder as $key => $detailOrder) {
                        $child = ChildMaster::find($detailOrder->child_id);
                        if ($child != null && $child->current_order_id == $datasOrder->order_id) {
                            $child->is_sponsored = 0;
                            $child->current_order_id = null;
                            $child->is_paid = 0;
                            $child->save();
                        }
                    }
                    // TO DO : SEND EMAIL CANCEL
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::channel('cron')->info('ERROR CRON JOB CekPaidorNot');
            Log::channel('cron')->error($e);
        }
    }
}
