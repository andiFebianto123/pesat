<?php
 
namespace App\Services\Midtrans;
 
use Midtrans\Snap;
 
class CreateSnapTokenService extends Midtrans
{
    protected $order;
    protected $code;
 
    public function __construct($order,$code)
    {
        
        parent::__construct();
 
        $this->order= $order;
        $this->code = $code;
        
    }
 
    public function getSnapToken()
    {

        $params = [
            'transaction_details' => [
                'order_id' => "anak-".$this->code,
                'gross_amount' =>$this->order[0]->total_price,
            ],
            'item_details' => [],

                'customer_details' => [
                'first_name' => $this->order[0]->sponsor_name,
                'email' => $this->order[0]->email,
                'phone' => $this->order[0]->no_hp,
            ],
            "expiry" => [
                "unit" => "day",
                "duration" => 2
            ]
        ];

        foreach ($this->order as $key => $detail) {
            $params['item_details'][] = array (  
                    'id' => $this->order[$key]->child_id,
                    'price' => $this->order[$key]->price,
                    'quantity' => 1,
                    'name' => $this->order[$key]->full_name,
            );
        }

        $snapToken = Snap::getSnapToken($params);
        return $snapToken;
    }
}
