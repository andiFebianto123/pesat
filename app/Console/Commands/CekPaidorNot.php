<?php

namespace App\Console\Commands;

use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use App\Models\DataOrder;
use App\Models\OrderDt;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
        $now = Carbon::now();
        $nowAdd2Days = $now->copy()->addDay(-2);

        $newDateFormat = date("Y-m-d", strtotime($nowAdd2Days));
        $datasOrder = DataDetailOrder::where('start_order_date',$newDateFormat)
        ->distinct()
        ->get('order_id');

        $datasChild = DataDetailOrder::where('start_order_date',$newDateFormat)
        ->get('child_id');
        
        foreach($datasOrder as $key => $datas){

            $orderHd = DataOrder::find($datas)->first();

            $orderHd->payment_status = 3;
            
            $orderHd->save();

        }        

        foreach($datasChild as $key => $datachild){

            $childmaster = ChildMaster::find($datachild)->first();
            $childmaster->is_sponsored = 0;
            $childmaster->current_order_id = null;
            $childmaster->save();
        }
        
       // return Command::SUCCESS;
    }
}
