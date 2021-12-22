<?php
 
namespace App\Services\Midtrans;
 
use Midtrans\Snap;
 
class CreateSnapTokenForProjectService extends Midtrans
{
    protected $order;
    protected $code;
 
    public function __construct($order,$code)
    {
        
        parent::__construct();
 
        $this->order= $order;
        $this->code = $code;
        
       // dd($this->order,$code);
    }
 
    public function getSnapToken()
    {
        
        $params = [
            'transaction_details' => [
                'order_id' => "proyek-".$this->code,
                'gross_amount' => $this->order[0]->price,
            ],
            'item_details' => [
                [
                    'id' => $this->order[0]->project_id,
                    'price' => $this->order[0]->price,
                    'quantity' => 1,
                    'name' => $this->order[0]->title,
                ],
                // [
                //     'id' => 2,
                //     'price' => '60001',
                //     'quantity' => 1,
                //     'name' => 'Memory Card VGEN 5GB',
                // ],

                //$this->order
            ],
            'customer_details' => [
                'first_name' => $this->order[0]->full_name,
                'email' => $this->order[0]->email,
                'phone' => $this->order[0]->no_hp,
            ]
        ];
 
        $snapToken = Snap::getSnapToken($params);
 
        return $snapToken;
    }
}
