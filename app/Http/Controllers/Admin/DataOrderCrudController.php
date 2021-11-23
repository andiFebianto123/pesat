<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DataOrderRequest;
use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use App\Models\OrderDt;
use App\Models\OrderHd;
use App\Models\Sponsor;
use App\Services\Midtrans\CreateSnapTokenService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class DataOrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DataOrderCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
 //   use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {destroy as traitDestroy;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {store as traitstore;}
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\DataOrder::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/data-order');
        CRUD::setEntityNameStrings('data order', 'data orders');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addButtonFromModelFunction('line', 'open_dlp', 'sponsoredchild', 'beginning');
        $this->crud->addButtonFromModelFunction('line', 'cekstatus', 'Cek_Status', 'last');
        
        $this->crud->addColumns([
            [
                'name' => 'order_id',
                'label' => 'Order ID',
            ],

            [
                'name' => 'sponsor_id',
                'label' => 'Sponsor',
                'entity' => 'sponsorname',
                'attribute' => 'full_name',
              
            ],

            [
                'name' => 'total_price',
                'label'=> 'Total',
                'prefix'=> 'Rp. ',

            ],
            [
                'name' => 'payment_status',
                'label' => 'Status Pembayaran',
                'type' => 'radio',
                'options' => [1 => 'Menungggu Pembayaran', 2 => 'Sukses',3 =>'Batal'],
              
            ],
        ]);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DataOrderRequest::class);

        $orderno=[
            'name'  => 'order_no',
            'type'  => 'hidden'
        ];
        $sponsor = [
            'name' => 'sponsor_id',
            'label' => "Nama Sponsor",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->sponsor(),

        ];

        $child = [
            'name' => 'child_id',
            'label' => "Nama Anak",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->child(),
        ];
        $subs =[
            'name'            => 'monthly_subscription',
            'label'           => "Durasi Subscribe",
            'type'            => 'select_from_array',
            'options'         => [1 => '1 Bulan', 3 => '3 Bulan', 6 => '6 Bulan',12=>'12 Bulan'],
            'allows_null'     => false,
            'allows_multiple' => false,

        ];
        $startOrderdate=[
            'name'  => 'start_order_date',
            'type'  => 'hidden'
        ];

        $endOrderdate=[
            'name'  => 'end_order_date',
            'type'  => 'hidden'
        ];
        $price=[
            'name'  => 'price',
            'type'  => 'hidden'
        ];

        $totalprice=[
            'name'  => 'total_price',
            'type'  => 'hidden'
        ];

        $this->crud->addFields([
            $orderno,$sponsor,$child,$subs,
            $startOrderdate,$endOrderdate,$price,$totalprice
    ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
       // $this->setupCreateOperation();
  
    $status =[
        'name'            => 'payment_status',
        'label'           => "Status Pembayaran",
        'type'            => 'select_from_array',
        'options'         => [1 => 'Menunggu Pembayaran', 2 => 'Sudah Dibayar', 3 => 'Kadaluarsa'],
        'allows_null'     => false,
        'allows_multiple' => false,

    ];


    $this->crud->addFields([
        $status
    ]);
    }

    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();


        do {
            $code = random_int(100000, 999999);
        } while (OrderHd::where("order_no", "=", $code)->first());

        $child = ChildMaster::where('child_id',$request->child_id)->first();
        $getPrice = $child->price;
        $subs = ($request->monthly_subscription);
        $totalPrice = $subs * $getPrice;

        $this->crud->getRequest()->request->set('order_no',$code);
        $this->crud->getRequest()->request->set('total_price',$totalPrice);

        // insert item in the db
        $item = $this->crud->create($this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;
        
        $startOrderdate = Carbon::now();
        $orders = new OrderDt();
        $orders->order_id           = $item->order_id;
        $orders->child_id           = $request->child_id;
        $orders->price              = $getPrice;
        $orders->monthly_subscription= $request->monthly_subscription;
        $orders->start_order_date   = $startOrderdate;
        $orders->end_order_date     = $startOrderdate->copy()->addMonthsNoOverflow($request->monthly_subscription);
        $orders->save();


//        dd($item->order_id);
        $Snaptokenorder = DB::table('order_hd')->where('order_hd.order_id',$item->order_id)
        ->join('sponsor_master as sm','sm.sponsor_id','=','order_hd.sponsor_id')
        ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
        ->join('child_master as cm','cm.child_id','=','odt.child_id')
        ->select(
            'order_hd.*', 
            'odt.*', 
            'cm.full_name',
            'sm.full_name as sponsor_name',
            'sm.email',
            'sm.no_hp'
            )
        ->get();
        
       
        $order = OrderHd::where('order_id',$item->order_id)->first();                        
        $midtrans = new CreateSnapTokenService($Snaptokenorder,$code);
        $snapToken = $midtrans->getSnapToken();
        $order->snap_token = $snapToken;
        $order->save();
        
        ChildMaster::where('child_id', $request->child_id)
        ->update(['is_sponsored' => 1]);
    

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    function destroy($id)
    {
       
        $this->crud->hasAccessOrFail('delete');
       
        $cekdetail = DataDetailOrder::where('order_id', $id);
        $order = $cekdetail->exists();
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        if ($order == true) {
            return response()->json(array('status' => 'error', 'msg' => 'Error!', 'message' => 'The selected data has already had relation with other data.'), 403);
        } else {
            return $this->crud->delete($id);

        }
    }

    public function child(){

        $getchild = ChildMaster::where('is_sponsored',0)->get();
        $collection = collect($getchild);
        $child = $collection->pluck('full_name', 'child_id') ? $collection->pluck('full_name', 'child_id') : 0 / null;
        return $child;
    }
    public function sponsor(){

        $getsponsor = Sponsor::get();
        $collection = collect($getsponsor);
        $sponsor = $collection->pluck('full_name', 'sponsor_id') ? $collection->pluck('full_name', 'sponsor_id') : 0 / null;
        return $sponsor;
    }
}
