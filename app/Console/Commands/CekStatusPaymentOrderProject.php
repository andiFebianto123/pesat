<?php

namespace App\Console\Commands;

use App\Models\OrderProject;
use App\Models\ProjectMaster;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CekStatusPaymentOrderProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:status';

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
        $orders = DB::table('order_project')
		            ->get();

        foreach($orders as $key => $order){
            $getTotalAmount = OrderProject::groupBy('project_id')
                                    ->where('project_id',$order->project_id)
                                    ->where('payment_status',2)                            
                                    ->selectRaw('sum(price) as sum_price')
                                    ->pluck('sum_price')
                                    ->first();
            ProjectMaster::where('project_id', $order->project_id)
                            ->update(['last_amount' => $getTotalAmount]);

            $getProjectMaster = ProjectMaster::where('project_id', $order->project_id)->first();

            if($getProjectMaster->amount <= $getProjectMaster->last_amount){

            ProjectMaster::where('project_id', $order->project_id)
                            ->update(['is_closed' =>1]);

            }
        }
 
    }
}
