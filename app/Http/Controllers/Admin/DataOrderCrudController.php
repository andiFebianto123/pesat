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

use function PHPUnit\Framework\countOf;

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
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {edit as traitedit;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {update as traitupdate;}
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

        $this->crud->addFields([
            [
                'name' => 'sponsor_id',
                'label' => "Nama Sponsor",
                'type' => 'select2_from_array',
                'allows_null' => false,
                'options' => $this->sponsor(),
    
            ],
            [   // repeatable
                'name'  => 'testimonials',
                'label' => 'List Order',
                'type'  => 'repeatable',
                'fields' => [
            [
            'name'  => 'order_no',
            'type'  => 'hidden'
            ],

            [
            'name' => 'child_id',
            'label' => "Nama Anak",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->child(),
            ],
            [
            'name'            => 'monthly_subscription',
            'label'           => "Durasi Subscribe",
            'type'            => 'select_from_array',
            'options'         => [1 => '1 Bulan', 3 => '3 Bulan', 6 => '6 Bulan',12=>'12 Bulan'],
            'allows_null'     => false,
            'allows_multiple' => false,

            ],
            [
            'name'  => 'start_order_date',
            'type'  => 'hidden'
            ],

            [
            'name'  => 'end_order_date',
            'type'  => 'hidden'
            ],
            [
            'name'  => 'price',
            'type'  => 'hidden'
            ],

            [
            'name'  => 'total_price',
            'type'  => 'hidden'
            ],
        
        ],
            
                // optional
                'new_item_label'  => 'Add Data', // customize the text of the button
               // 'init_rows' => 2, // number of empty rows to be initialized, by default 1
                'min_rows' => 1, // minimum rows allowed, when reached the "delete" buttons will be hidden
                //'max_rows' => 2, // maximum rows allowed, when reached the "new item" button will be hidden
            
            ]
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

    $status =[
        'name'            => 'payment_status',
        'label'           => "Status Pembayaran",
        'type'            => 'select_from_array',
        'options'         => [1 => 'Menunggu Pembayaran', 2 => 'Sudah Dibayar', 3 => 'Kadaluarsa'],
        'allows_null'     => false,
        'allows_multiple' => false,

    ];

    $sponsor = [
        'name'    => 'sponsor_id',
        'type'    => 'select',
        'label'   => 'Nama Sponsor',
        'entity'  => 'sponsorname',
        'attribute'=>'full_name'
    ];

    $dataorder =[   // repeatable
        'name'  => 'dataorder',
        'label' => 'Testimonials',
        'type'  => 'repeatable',
        'fields' => [
            
            [
                'name'    => 'order_dt_id',
                'type'    => 'hidden',
            ],

            [
                'name'    => 'child_id',
                'type'    => 'select',
                'label'   => 'Nama Anak',
                'entity'  =>  'childnamewithcondition',
                'attribute'=> 'full_name',
                'allows_null'=> false
            ],
            [
                'name'    => 'monthly_subscription',
                'label'   => 'Durasi Subscribe',
                'type'    => 'select_from_array',
                'options' => [1 => '1 Bulan', 3 => '3 Bulan', 6 => '6 Bulan',12=>'12 Bulan'],
                'allows_null'     => false,
                'allows_multiple' => false,
            ],

        ],
        'new_item_label'  => 'Add Data', // customize the text of the button
       // 'init_rows' => 2, // number of empty rows to be initialized, by default 1
        //'min_rows' => 2, // minimum rows allowed, when reached the "delete" buttons will be hidden
        //'max_rows' => 2, // maximum rows allowed, when reached the "new item" button will be hidden
    
    ];


    $this->crud->addFields([
        $sponsor,$status,$dataorder
    ]);
    }

    public function edit($id)
    {
        $getStatus = OrderHd::where('order_id',$id)->first();
        $getStatusPayment = $getStatus->payment_status;
        if($getStatusPayment==2){
            
            \Alert::error(trans('Tidak bisa ubah data, karena sudah ada pembayaran'))->flash();
            return redirect()->back();
        }
        $this->crud->hasAccessOrFail('update');
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        
        // get the info for that entry
        $getOrderDt = OrderDt::where('order_id',$id)
                                ->where('deleted_at',null)
                                ->get();
        $orderDt =json_encode($getOrderDt);
        $fields =$this->crud->getUpdateFields();
        $fields['dataorder']['value']=$orderDt;
        $this->crud->setOperationSetting('fields', $fields);
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit').' '.$this->crud->entity_name;

        $this->data['id'] = $id;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getEditView(), $this->data);
    }

    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        
        $getDatas = $request->testimonials;
        $datas = json_decode($getDatas);

        $uniquedata = array_unique($datas,SORT_REGULAR);
        $sponsorid = $request->sponsor_id;

        $id = DB::table('order_hd')->insertGetId([
            'sponsor_id' => $sponsorid,
            'payment_status' => 1,
            'total_price'   => 0

        ]);
        
        foreach($uniquedata as $key => $data){
        $child = ChildMaster::where('child_id',$data->child_id)->first();
        $getPrice = $child->price;
        $subs = ($data->monthly_subscription);
        $totalPrice = $subs * $getPrice;
        $this->crud->getRequest()->request->set('total_price',$totalPrice);

        
        $startOrderdate = Carbon::now();
        $orders = new OrderDt();
        $orders->order_id           = $id;
        $orders->child_id           = $data->child_id;
        $orders->price              = $getPrice;
        $orders->monthly_subscription= $data->monthly_subscription;
        $orders->start_order_date   = $startOrderdate;
        $orders->end_order_date     = $startOrderdate->copy()->addMonthsNoOverflow($data->monthly_subscription);
        $orders->save();

        $Snaptokenorder = DB::table('order_hd')->where('order_hd.order_id',$id)
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
        
            
        ChildMaster::where('child_id', $data->child_id)
        ->update(['is_sponsored' => 1]);
    

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();
        }
        $getTotalPrice = OrderDt::groupBy('order_id')
                    ->where('order_id',$id)
                    ->selectRaw('sum(price) as sum_price')
                    ->pluck('sum_price')
                    ->first();
        $order = OrderHd::where('order_id',$id)->first();                        
        $midtrans = new CreateSnapTokenService($Snaptokenorder,$id);
        $snapToken = $midtrans->getSnapToken();
        $order->snap_token = $snapToken;
        $order->total_price=$getTotalPrice;
        $order->save();

        return redirect()->back();
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db
        $getDataOrder = $request->dataorder;

        $orderDecodes  = json_decode($getDataOrder);
        $x=array_column($orderDecodes, 'order_dt_id');
        $getData = DataDetailOrder::where('order_id',$request->order_id)
                                ->where('deleted_at',null)
                                ->get();
        $arraygetData = $getData->toArray();
    
        $getIdData = array_column($arraygetData,'order_dt_id');
        $getIddeletedDatas = array_diff($getIdData, $x);


        foreach($getIddeletedDatas as $key => $datadeleted){
            $deletedorder = DataDetailOrder::where('order_dt_id',$datadeleted)->first();
            $deletedorder->deleted_at = Carbon::now();
            $deletedorder->save();

        }
        
        // OrderDt::whereIn('order_dt_id', $getIddeletedDatas)->update([
        //     'deleted_at' => Carbon::now()
        //   ]);

        foreach($orderDecodes as $key => $orderDecode){
            
            if($orderDecode->order_dt_id == '' || $orderDecode->order_dt_id == null){
                
                
                $child = ChildMaster::where('child_id',$orderDecode->child_id)->first();
                $startOrderdate = Carbon::now();
                $getPrice = $child->price;
                $subs = $orderDecode->monthly_subscription;
                $totalPrice = $getPrice * $subs;

                $endOrderDate     = $startOrderdate->copy()->addMonthsNoOverflow($orderDecode->monthly_subscription);

                $orderdt = new DataDetailOrder();
                $orderdt->order_id = $request->order_id;
                $orderdt->child_id = $orderDecode->child_id;
                $orderdt->monthly_subscription = $orderDecode->monthly_subscription;
                $orderdt->price    = $totalPrice;
                $orderdt->start_order_date = $startOrderdate;
                $orderdt->end_order_date   = $endOrderDate;
                $orderdt->save();
            }
            $deletedOrders = DataDetailOrder::where('order_id',$request->order_id)->get();

            $deletedorder = DataDetailOrder::where('order_dt_id',$orderDecode->order_dt_id)->first();
            $deletedorder->child_id = $orderDecode->child_id;
            $deletedorder->monthly_subscription = $orderDecode->monthly_subscription;
            $deletedorder->save();

            
            // DataDetailOrder::where('order_dt_id', $orderDecode->order_dt_id)
            //         ->update(['child_id' => $orderDecode->child_id,
            //               'monthly_subscription' =>  $orderDecode->monthly_subscription,
            //     ]);

        }



        $getTotalPrice = OrderDt::groupBy('order_id')
        ->where('order_id',$request->order_id)
        ->selectRaw('sum(price) as sum_price')
        ->pluck('sum_price')
        ->first();

        $orderHd = OrderHd::where('order_id',$request->order_id)->first();
        $orderHd->sponsor_id = $request->sponsor_id;
        $orderHd->payment_status = $request->payment_status;
        $orderHd->total_price=$getTotalPrice;
        $orderHd->save();
        

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($orderHd->getKey());
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
