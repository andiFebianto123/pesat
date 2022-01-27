<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use App\Models\OrderProject;
use App\Models\ProjectMaster;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CekPaidorNotForProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:paidornotforproject';

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

            $datasOrder = OrderProject::where('created_at', '<', $nowAdd2Days)
                ->where('order_project.payment_status', 1)
                ->get();
            foreach ($datasOrder as $key => $data) {
                $cancelSuccess = false;
                $updateStatusMidtrans = false;
                try {
                    \Midtrans\Transaction::cancel($data->order_project_id_midtrans);
                    $cancelSuccess = true;
                    $updateStatusMidtrans = true;
                } catch (Exception $e) {
                    if ($e->getCode() == 404) {
                        $cancelSuccess = true;
                    }
                    else if($e->getCode() == 412){
                        try{
                            $decoderespon = \Midtrans\Transaction::status($data->order_project_id_midtrans);
                            if($decoderespon->transaction_status == 'expire'){
                                $cancelSuccess = true;
                            }
                        }
                        catch(Exception $e){

                        }
                    }
                }

                if ($cancelSuccess) {
                    $data->payment_status = 3;
                    if($updateStatusMidtrans){
                        $data->status_midtrans = 'cancel';
                    }
                    $data->save();

                    $getProjectId = $data->project_id;
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

                    // TO DO : SEND EMAIL CANCEL
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::channel('cron')->info('ERROR CRON JOB CekPaidorNotForProject');
            Log::channel('cron')->error($e);
        }
    }
}
