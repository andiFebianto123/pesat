<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DataOrderRequest;
use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use App\Models\DataOrder;
use App\Models\OrderDt;
use App\Models\OrderHd;
use App\Models\Sponsor;
use App\Services\Midtrans\CreateSnapTokenService;
use App\Traits\RedirectCrud;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class DataOrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DataOrderCrudController extends CrudController
{
    use RedirectCrud;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    //use Backpack\CRUD\app\Library\Widget;
    //   use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    //   use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
  //  use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {destroy as traitDestroy;}
  use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {create as traitcreate;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {store as traitstore;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {edit as traitedit;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {update as traitupdate;}
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    function setup()
    {
        CRUD::setModel(\App\Models\DataOrder::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/data-order');
        CRUD::setEntityNameStrings('data order', 'data orders');

    }

// alternatively, use a fluent syntax to define each widget attribute

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    function setupListOperation()
    {
        $this->crud->addButtonFromModelFunction('line', 'open_dlp', 'sponsoredchild', 'beginning');
        $this->crud->addButtonFromModelFunction('line', 'cekstatus', 'Cek_Status', 'last');
        $this->crud->addButtonFromModelFunction('line', 'cancel', 'cancelOrder', 'last');

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
                'label' => 'Total',
                'prefix' => 'Rp. ',

            ],
            [
                'name' => 'payment_status',
                'label' => 'Status Pembayaran',
                'type' => 'radio',
                'options' => [1 => 'Menungggu Pembayaran', 2 => 'Sukses', 3 => 'Batal'],

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
    function setupCreateOperation()
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
            [ // repeatable
                'name' => 'testimonials',
                'label' => 'List Order',
                'type' => 'repeatable_create_child',
                'fields' => [
                    [
                        'name' => 'order_no',
                        'type' => 'hidden',
                    ],

                    [
                        'name' => 'child_id',
                        'label' => "Nama Anak",
                        'type' => 'select2_from_array',
                        'allows_null' => false,
                        'options' => [],
                        'attributes'=>[
                            'disabled'=>true
                        ],
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-6',
                        ],
                    ],
                    [
                        'name' => 'price',
                        'label'=> 'Biaya / Bulan',
                        'type' => 'text',
                        'prefix'=> 'Rp. ',
                        'attributes'=>[
                            'disabled'=>true
                        ],
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-6',
                        ],
                    ],
                    [
                        'name' => 'monthly_subscription',
                        'label' => "Durasi Subscribe",
                        'type' => 'select2_from_array',
                        'options' => [1 => '1 Bulan', 3 => '3 Bulan', 6 => '6 Bulan', 12 => '12 Bulan'],
                        'allows_null' => false,
                        'allows_multiple' => false,

                    ],
                    [
                        'name' => 'start_order_date',
                        'type' => 'hidden',
                    ],

                    [
                        'name' => 'end_order_date',
                        'type' => 'hidden',
                    ],
                    [
                        'name' => 'price',
                        'type' => 'hidden',
                    ],

                    [
                        'name' => 'total_price',
                        'type' => 'hidden',
                    ],

                ],

                // optional
                'new_item_label' => 'Add Data', // customize the text of the button
                // 'init_rows' => 2, // number of empty rows to be initialized, by default 1
                'min_rows' => 0, // minimum rows allowed, when reached the "delete" buttons will be hidden
                //'max_rows' => 2, // maximum rows allowed, when reached the "new item" button will be hidden

            ],
            [
                'name' => 'empty',
                'type' => 'hidden',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6',
                ],


            ],

            [
                'name' => 'totalprice',
                'label' => "Total Price",
                'type' => 'text_for_order',
                'prefix'=> 'Rp.',
                'default'=> 0,
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6',
                ],
                'attributes'=>[
                    'disabled'=>true,
                    'id' => 'totalprice'
                  ]


            ],

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
    function setupUpdateOperation()
    {

        $sponsor = [
            'name' => 'sponsor_id',
            'type' => 'select',
            'label' => 'Nama Sponsor',
            'entity' => 'sponsorname',
            'attribute' => 'full_name',
        ];

        $orderid = [
            'name' => 'order_id',
            'type' => 'text',
            'label' => 'No Order',
            'attributes'=>[
                'disabled'=>true
            ],
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],        
        ];

        $email =[
            'name' => 'email',
            'label' => 'Email',
            'attributes'=>[
                'disabled'=>true
            ],            
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $nowa = [
            'name' => 'no_hp',
            'type' => 'text',
            'label' => 'No Wa',
            'attributes'=>[
                'disabled'=>true
            ],
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

        ];
        $address = [
            'name' => 'address',
            'type' => 'text',
            'label' => 'Alamat',
            'attributes'=>[
                'disabled'=>true
            ],
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

        ];

        $dataorder = [ // repeatable
            'name' => 'dataorder',
            'label' => 'List Order',
            'type' => 'repeatable_edit_child',
            'fields' => [

                [
                    'name' => 'order_dt_id',
                    'type' => 'hidden',
                ],

                [
                    'name' => 'child_id',
                    'label' => "Nama Anak",
                    'type' => 'select_from_array',
                    'attribute' => 'full_name',
                    'options' => [],
                    'allows_null' => false,
                    'attributes'=>[
                        'disabled'=>true
                    ],
                      'wrapperAttributes' => [
                        'class' => 'form-group col-md-6',
                    ],

                ],
                [
                    'name' => 'child_id',
                    'label'=> 'Biaya / Bulan',
                    'type' =>  'select_from_array',//'text',
                    'attribute'=>'price',
                    'prefix'=> 'Rp. ',
                    'attributes'=>[
                        'disabled'=>true
                    ],
                    'wrapperAttributes' => [
                        'class' => 'form-group col-md-6',
                    ],
                ],

                [
                    'name' => 'monthly_subscription',
                    'label' => 'Durasi Subscribe',
                    'type' => 'select2_from_array',
                    'options' => [1 => '1 Bulan', 3 => '3 Bulan', 6 => '6 Bulan', 12 => '12 Bulan'],
                    'allows_null' => false,
                    'allows_multiple' => false,
                ],

            ],
            'new_item_label' => 'Add Data', // customize the text of the button
            // 'init_rows' => 2, // number of empty rows to be initialized, by default 1
            //'min_rows' => 2, // minimum rows allowed, when reached the "delete" buttons will be hidden
            //'max_rows' => 2, // maximum rows allowed, when reached the "new item" button will be hidden

        ];
        $space =             [
            'name' => 'empty',
            'type' => 'hidden',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],


        ];

        $totalPrice =[
            'name' => 'totalprice',
            'label' => "Total Price",
            'type' => 'text',
            'prefix'=> 'Rp.',
            'default'=>$this->sumprice($this->crud->getCurrentEntryId()),
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
            'attributes'=>[
                'disabled'=>true,
                'id' => 'totalprice'
              ]


            ];

        $this->crud->addFields([
            $sponsor, $orderid,$email,$nowa,$address,$dataorder,$space,$totalPrice
        ]);
    }

    function edit($id)
    {
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $getStatus = DataOrder::where('order_id', $id)->first();
        $getStatusPayment = $getStatus->payment_status;
        $getSponsor = Sponsor::where('sponsor_id',$getStatus->sponsor_id)->first();
        $getEmail = $getSponsor->email;
        $getNoWa = $getSponsor->no_hp;
        $getAddress = $getSponsor->address;

        $getStatusMidtrans = $getStatus->order_id_midtrans;

        try{


        $decoderespon = \Midtrans\Transaction::status($getStatusMidtrans);
        if($decoderespon->transaction_status){

            \Alert::error(trans('Tidak bisa ubah data, no order sudah terdaftar di payment gateway'))->flash();
            return redirect()->back();

        }
    }catch(Exception $e){

    }

        if ($getStatusPayment == 2) {

            \Alert::error(trans('Tidak bisa ubah data, karena sudah ada pembayaran'))->flash();
            return redirect()->back();
        }
        $this->crud->hasAccessOrFail('update');
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry
        $getOrderDt = DataDetailOrder::where('order_id', $id)
            ->get();
        

        $orderDt = json_encode($getOrderDt);
       
        $child = $getOrderDt->pluck('child_id');

        $fields = $this->crud->getUpdateFields();

        $childs = $this->child($child,true);
        $childPrice = $this->childprice($child);
        $priceChilds = $childs->pluck('price','child_id');


        $fields['dataorder']['fields'][2]['options']=$childPrice;
        
        $fields['email']['value'] = $getEmail;
        $fields['no_hp']['value'] = $getNoWa;
        $fields['address']['value'] = $getAddress;
        $fields['dataorder']['value'] = $orderDt;
        $fields['dataorder']['fields'][1]['options'] = $childs;
        $this->crud->setOperationSetting('fields', $fields);

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
     
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit') . ' ' . $this->crud->entity_name;
    
        $this->data['id'] = $id;

        $this->data['childs'] = $childs;
        $this->data['childForPrice']= $childPrice;
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getEditView(), $this->data);


}

    function create()
    {
        $this->crud->hasAccessOrFail('create');

        $fields = $this->crud->getCreateFields();

        $childs = $this->child(null,null);

        $optionChilds= $childs->pluck('full_name','child_id');

        $priceChilds = $childs->pluck('price','child_id');

        $fields['testimonials']['fields'][1]['options'] = $optionChilds;
        $this->crud->setOperationSetting('fields', $fields);

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;
        $this->data['childs'] = $optionChilds;
        $this->data['childForPrice']  = $priceChilds;
        
        
    // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getCreateView(), $this->data);
    }


    function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        $getDatas = $request->testimonials;
        $datas = json_decode($getDatas);

        $sponsorid = $request->sponsor_id;

        $childs = [];
        $error = [];
        $index = 1;

        $uniquedata = [];

        if (JSON_ERROR_NONE !== json_last_error() || !is_array($datas) || count($datas) == 0){

            return $this->redirectStoreCrud(['message' => 'Detail order tidak boleh kosong']);            
        }
        foreach ($datas as $key => $orderDecode) {

            $child = ChildMaster::where('child_id', $orderDecode->child_id)->first();
            if ($child == null) {
                $error[] = 'Detail order ke ' . $index . ' : Anak tidak ditemukan';
            } elseif ($child->is_sponsored == 1) {
                $error[] = 'Detail order ke ' . $index . ' : Anak sudah disponsori';

            } else {
                $childs[$child->child_id] = $child;
                $uniquedata[$child->child_id] = $orderDecode;
            }
            if ($orderDecode->monthly_subscription != 1 && $orderDecode->monthly_subscription != 3 && $orderDecode->monthly_subscription != 6 && $orderDecode->monthly_subscription != 12) {
                $error[] = 'Detail order ke ' . $index . ' : Durasi subscribe tidak valid';

            }
            $index++;

        }
        if (count($error) != 0) {

            return $this->redirectStoreCrud(['message' => $error]);
        }
        DB::beginTransaction();
        try {
            $id = DB::table('order_hd')->insertGetId([
                'sponsor_id' => $sponsorid,
                'payment_status' => 1,
                'total_price' => 0,

            ]);

            foreach ($uniquedata as $key => $data) {
                $child = $childs[$data->child_id];
                $getPrice = $child->price;
                $subs = ($data->monthly_subscription);
                $totalPrice = $subs * $getPrice;
                $this->crud->getRequest()->request->set('total_price', $totalPrice);

                $startOrderdate = Carbon::now();
                $orders = new DataDetailOrder();
                $orders->order_id = $id;
                $orders->child_id = $data->child_id;
                $orders->price = $totalPrice;
                $orders->monthly_subscription = $data->monthly_subscription;
                $orders->start_order_date = $startOrderdate;
                $orders->end_order_date = $startOrderdate->copy()->addMonthsNoOverflow($data->monthly_subscription);
                $orders->save();

                $Snaptokenorder = DB::table('order_hd')->where('order_hd.order_id', $id)
                    ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_hd.sponsor_id')
                    ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
                    ->join('child_master as cm', 'cm.child_id', '=', 'odt.child_id')
                    ->select(
                        'order_hd.*',
                        'odt.*',
                        'cm.full_name',
                        'sm.full_name as sponsor_name',
                        'sm.email',
                        'sm.no_hp'
                    )
                    ->get();

                 
                $child->is_sponsored = 1;
                $child->current_order_id = $id;    
                $child->save();

            }
            $getTotalPrice = DataDetailOrder::groupBy('order_id')
                ->where('order_id', $id)
                ->selectRaw('sum(price) as sum_price')
                ->pluck('sum_price')
                ->first();

            $order = DataOrder::where('order_id', $id)->first();
            $midtrans = new CreateSnapTokenService($Snaptokenorder, $id);
            $snapToken = $midtrans->getSnapToken();
            $order->snap_token = $snapToken;
            $order->order_id_midtrans = 'anak-'.$id;
            $order->total_price = $getTotalPrice;
            $order->save();
            DB::commit();
            \Alert::success(trans('backpack::crud.insert_success'))->flash();
            $this->crud->setSaveAction();

            return $this->crud->performSaveAction($id);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db
        $getDataOrder = $request->dataorder;

        $orderDecodes = json_decode($getDataOrder);

        $cekStatusPayment = DataOrder::where('order_id', $request->order_id)->first();
        if ($cekStatusPayment == null) {

            \Alert::error('Data order tidak ditemukan')->flash();
            return redirect($this->crud->route);

        } elseif ($cekStatusPayment->payment_status == 2) {
            
            \Alert::error('Data order sudah dibayar')->flash();
            return redirect($this->crud->route);
        
        }elseif(JSON_ERROR_NONE !== json_last_error() || !is_array($orderDecodes) || count($orderDecodes) == 0){

            return $this->redirectUpdateCrud($request->order_id, ['message' => 'Detail order tidak boleh kosong']);            
        }

        $childs = [];
        $error = [];
        $index = 1;
        $intOrderId = (int)$request->order_id;
      
        foreach ($orderDecodes as $key => $orderDecode) {
            $child = ChildMaster::where('child_id', $orderDecode->child_id)->first();

            if ($child == null) {
                $error[] = 'Detail order ke ' . $index . ' : Anak tidak ditemukan';
            }  elseif($child->is_sponsored == 1 && $child->current_order_id != $intOrderId){
                $error[] = 'Detail order ke '.$index.' : Anak sudah disponsori';

            }
            else {

                $childs[$child->child_id] = $child;

            }
            if ($orderDecode->monthly_subscription != 1 && $orderDecode->monthly_subscription != 3 && $orderDecode->monthly_subscription != 6 && $orderDecode->monthly_subscription != 12) {
                $error[] = 'Detail order ke ' . $index . ' : Durasi subscribe tidak valid';

            }
        $index++;
        }
        if (count($error) != 0) {

            return $this->redirectUpdateCrud($request->order_id, ['message' => $error]);
        }
        DB::beginTransaction();
        try {
            $orderdetailid = [];
            foreach ($orderDecodes as $key => $orderDecode) {

                $orderdt = DataDetailOrder::where('order_id', $request->order_id)
                    ->where('child_id', $orderDecode->child_id)
                    ->first();

                if ($orderdt == null) {

                    $orderdt = new DataDetailOrder();
                }

                $child = $childs[$orderDecode->child_id];
                $child->is_sponsored = 1;
                $child->current_order_id = $request->order_id;
                $child->save();

                $startOrderdate = Carbon::now();
                $getPrice = $child->price;
                $subs = $orderDecode->monthly_subscription;
                $totalPrice = $getPrice * $subs;
                $endOrderDate = $startOrderdate->copy()->addMonthsNoOverflow($orderDecode->monthly_subscription);
                $orderdt->order_id = $request->order_id;
                $orderdt->child_id = $orderDecode->child_id;
                $orderdt->monthly_subscription = $orderDecode->monthly_subscription;
                $orderdt->price = $totalPrice;
                $orderdt->start_order_date = $startOrderdate;
                $orderdt->end_order_date = $endOrderDate;
                $orderdt->save();

                $orderdetailid[] = $orderdt->order_dt_id;

            }

            $getDeletedOrder = DataDetailOrder::where('order_id', $request->order_id)
                ->whereNotIn('order_dt_id', $orderdetailid)->get();

            foreach ($getDeletedOrder as $key => $datadeleted) {
                $child = ChildMaster::where('child_id', $datadeleted->child_id)->first();
                $child->is_sponsored = 0;
                $child->current_order_id = null;
                $child->save();

                $datadeleted->delete();

            }
            $getTotalPrice = DataDetailOrder::groupBy('order_id')
                ->where('order_id', $request->order_id)
                ->selectRaw('sum(price) as sum_price')
                ->pluck('sum_price')
                ->first();

            $orderHd = DataOrder::where('order_id', $request->order_id)->first();
            $orderHd->sponsor_id = $request->sponsor_id;
           // $orderHd->payment_status = $request->payment_status;
            $orderHd->total_price = $getTotalPrice;
            $orderHd->save();
            DB::commit();

            // show a success message
            \Alert::success(trans('backpack::crud.update_success'))->flash();

            // save the redirect choice for next time

            $Snaptokenorder = DB::table('order_hd')->where('order_hd.order_id', $request->order_id)
            ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_hd.sponsor_id')
            ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
            ->join('child_master as cm', 'cm.child_id', '=', 'odt.child_id')
            ->select(
                'order_hd.*',
                'odt.*',
                'cm.full_name',
                'sm.full_name as sponsor_name',
                'sm.email',
                'sm.no_hp'
            )
            ->get();

            $getTotalPrice = DataDetailOrder::groupBy('order_id')
            ->where('order_id', $request->order_id)
            ->selectRaw('sum(price) as sum_price')
            ->pluck('sum_price')
            ->first();

            $order = DataOrder::where('order_id', $request->order_id)->first();
            $midtrans = new CreateSnapTokenService($Snaptokenorder, $request->order_id);
            $snapToken = $midtrans->getSnapToken();
            $order->snap_token = $snapToken;
            $order->total_price = $getTotalPrice;
            $order->save();

            $this->crud->setSaveAction();

            return $this->crud->performSaveAction($orderHd->getKey());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

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

    function child($child,$isPluck)
    {

        $getchild = ChildMaster::where('is_sponsored', 0)
            ->when($child != null, function ($query) use ($child) {
                $query->orWhereIn('child_id', $child);
            })
            ->get();

 if($isPluck ==true){
    return $getchild->pluck('full_name', 'child_id');
 }else{

    return $getchild;//->pluck('full_name', 'child_id');
 }

    }

    function childprice($child)
    {

        $getchild = ChildMaster::where('is_sponsored', 0)
            ->when($child != null, function ($query) use ($child) {
                $query->orWhereIn('child_id', $child);
            })
            ->get();

    return $getchild->pluck('price', 'child_id');
 
    }
    
    function sumprice($id){
       
                $getTempTotal = DataDetailOrder::where('order_id', $id)
                                    ->groupBy('order_dt.order_id')
                                    ->selectRaw('sum(price) as sum_price')
                                    ->pluck('sum_price')
                                    ->first();
                                 //   dd($getTempTotal);
                return $getTempTotal;//->pluck('order_id','sumprice');
    }
    function sponsor()
    {

        $getsponsor = Sponsor::get();
        $collection = collect($getsponsor);
        $sponsor = $collection->pluck('full_name', 'sponsor_id') ? $collection->pluck('full_name', 'sponsor_id') : 0 / null;
        return $sponsor;
    }
}
