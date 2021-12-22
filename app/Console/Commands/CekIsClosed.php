<?php

namespace App\Console\Commands;

use App\Models\OrderProject;
use App\Models\ProjectMaster;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CekIsClosed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:isclosed';

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
 
        $datasOrder = ProjectMaster::where('is_closed',0)
                        ->get();

            foreach($datasOrder as $key => $data){



                    $orderProject = OrderProject::where('project_id',$data->project_id)
                        ->where('payment_status',2)
                        ->groupBy('project_id')
                        ->selectRaw('sum(price) as sum_price')
                        ->pluck('sum_price');
              
                    $amount = intval($data->amount);
                    $totalPrice = intval($orderProject[0]);

                    $now = Carbon::now();
            if($data->end_date !== null){

                if($totalPrice >= $amount || $now > $data->end_date){// 

                    $projectMaster = ProjectMaster::find($data->project_id);
                    $projectMaster->is_closed = 1;
                    $projectMaster->save();

            }
        }else{
                if($totalPrice >= $amount){// 

                    $projectMaster = ProjectMaster::find($data->project_id);
                    $projectMaster->is_closed = 1;
                    $projectMaster->save();

            }
        }
    }

    }
}
