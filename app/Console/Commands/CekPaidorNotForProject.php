<?php

namespace App\Console\Commands;

use App\Models\OrderProject;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $now = Carbon::now();
        $nowAdd2Days = $now->copy()->addDay(-2);


        $datasOrder = OrderProject::where('created_at','<=',$nowAdd2Days)
                    ->where('order_project.payment_status',1)
                    ->get('order_project.order_project_id');
    
     
        DB::beginTransaction();
        try{

        foreach($datasOrder as $key => $datas){

            $orderProject = OrderProject::find($datas)->first();
           
            $orderProject->payment_status = 3;
          
            $orderProject->save();
          
            \Midtrans\Transaction::cancel($orderProject->order_project_id_midtrans);     
            DB::commit();    
      
        }        
    }catch(Exception $e){
      

         if($e->getCode() !== 404){

            $errorMessage = array('order_project_id' => $orderProject->order_project_id_midtrans, 'ErrorCode' => $e->getCode(),'ErrorMessage'=>$e->getMessage());

            \Log::channel('logstatusmidtrans')->info(json_encode($errorMessage));

            DB::rollBack();
           
        }else{
           
            DB::commit();

        }
//        return Command::SUCCESS;
    }
}
}