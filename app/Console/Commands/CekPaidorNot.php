<?php

namespace App\Console\Commands;

use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use App\Models\DataOrder;
use App\Models\OrderDt;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $now = Carbon::now();
        $nowAdd2Days = $now->copy()->addDay(-2);

        $newDateFormat = date("Y-m-d", strtotime($nowAdd2Days));

        $datasOrder = DataDetailOrder::where('start_order_date','<=',$newDateFormat)
                    ->join('order_hd as ohd','ohd.order_id','=','order_dt.order_id')
                    ->where('ohd.payment_status',1)
                    ->distinct()
                    ->get('ohd.order_id');
                
        $datasChild = DataDetailOrder::where('start_order_date',$newDateFormat)
                    ->get('child_id');
     
        DB::beginTransaction();
        try{

        foreach($datasOrder as $key => $datas){

            $orderHd = DataOrder::find($datas)->first();
           
            $orderHd->payment_status = 3;
          
            $orderHd->save();

          
            \Midtrans\Transaction::cancel($orderHd->order_id_midtrans);     
            DB::commit();    
      
        }        
    }catch(Exception $e){
      

         if($e->getCode() !== 404){

            $errorMessage = array('order_id' => $orderHd->order_id, 'ErrorCode' => $e->getCode(),'ErrorMessage'=>$e->getMessage());

            \Log::channel('logstatusmidtrans')->info(json_encode($errorMessage));

            DB::rollBack();
           
        }else{
           
            DB::commit();

        }
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
