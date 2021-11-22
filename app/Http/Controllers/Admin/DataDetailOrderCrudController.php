<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DataDetailOrderRequest;
use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use App\Models\DataOrder;
use App\Models\OrderDt;
use App\Traits\RedirectCrud;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * Class DataDetailOrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DataDetailOrderCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {store as traitstore;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {update as traitupdate;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation {show as traitshow;}


    use RedirectCrud;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\DataDetailOrder::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/data-detail-order');
        CRUD::setEntityNameStrings('data detail order', 'data detail orders');

        $this->crud->order_id = \Route::current()->parameter('header_id');

        DataDetailOrder::addGlobalScope('header_id', function (Builder $builder) {
            $builder->where('order_id', $this->crud->order_id);
        });
        CRUD::setModel(DataDetailOrder::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/detail-sponsor/' . ($this->crud->order_id ?? '-') . '/detail');
        CRUD::setEntityNameStrings('Add Child', 'Add Child');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
        $this->crud->addColumns([
    
            [
                'name' => 'child_id',
                'label' => 'Nama Anak',
                'entity' => 'childname',
                'attribute' => 'full_name',
            ],
            [
                'name' => 'price',
                'label'=> 'Total',
                'prefix'=> 'Rp. ',
            ],
            [
                'name' => 'monthly_subscription',
                'label'=> 'Durasi (Bulan)'
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
        CRUD::setValidation(DataDetailOrderRequest::class);

        $orderid = $this->crud->order_id;
       
        $child = [
            'name' => 'child_id',
            'label' => "Nama Anak",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->child(),
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
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
        $order = [
            'name' => 'order_id',
            'type' => 'hidden',
            'default' => $orderid,
            'label' => "id",
        ];
        $this->crud->addFields([
            $child,$subs,$order,$startOrderdate,$endOrderdate,$price
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
        $this->setupCreateOperation();
    }
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // insert item in the db
        $startOrderdate = Carbon::now();
        $subs = $request->input('monthly_subscription');
        $child = ChildMaster::where('child_id',$request->child_id)->first();
        $getPrice= $child->price;
        $TotalPrice = $subs * $getPrice;
       // dd($TotalPrice);
        $this->crud->getRequest()->request->set('start_order_date',$startOrderdate);
        
        $this->crud->getRequest()->request->set('end_order_date',$startOrderdate->copy()->addMonthsNoOverflow($subs));
        $this->crud->getRequest()->request->set('price', $TotalPrice);
       
        $error = [];

        $cekchild = ChildMaster::where('child_id',$request->child_id);
        $child = $cekchild->exists();
        
        

        if (!$child) {
            $error['child_id'] = ['The selected Child is not valid'];
        }

        if (count($error) > 0) {
            return $this->redirectStoreCrud($error);
        }
        

        $item = $this->crud->create($this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;
        
        // Updtae total_price order_hd
        $TotalPrice = DataDetailOrder::where('order_id',$request->order_id)
        ->groupBy('order_id')
      
        ->selectRaw('sum(price) as sum_price')
        ->pluck('sum_price')
        ->first();

        DataOrder::where('order_id', $request->order_id)
            ->update(['total_price' => $TotalPrice]);
        
        ChildMaster::where('child_id', $request->child_id)
            ->update(['is_sponsored' => 1]);
        
        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db
        $subs = $request->input('monthly_subscription');
        $TotalPrice = $subs * 150000;
        $this->crud->getRequest()->request->set('price', $TotalPrice);
       

        $error = [];

        $cekchild = ChildMaster::where('child_id',$request->child_id);
        $child = $cekchild->exists();
        
        

        if (!$child) {
            $error['child_id'] = ['The selected Child is not valid'];
        }

        if (count($error) > 0) {
            return $this->redirectStoreCrud($error);
        }


        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

          // Updtae total_price order_hd
          $TotalPrice = DataDetailOrder::where('order_id',$request->order_id)
          ->groupBy('order_id')
        
          ->selectRaw('sum(price) as sum_price')
          ->pluck('sum_price')
          ->first();
  
          DataOrder::where('order_id', $request->order_id)
              ->update(['total_price' => $TotalPrice]);

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->addColumns([
           
            [
                'name' => 'child_id',
                'label' => 'Nama Anak',
                'entity' => 'childname',
                'attribute' => 'full_name',
            ],
            [
                'name' => 'price',
                'label'=> 'Total',
                'prefix'=> 'Rp. ',
            ],
            [
                'name' => 'monthly_subscription',
                'label'=> 'Durasi (Bulan)'
            ],

        ]);

    }
    function child()
    {
        $getchild = ChildMaster::whereNotIn('child_id', function($query){
            $query->select('child_id')
            ->from(with(new OrderDt())->getTable())
            ->where('deleted_at', null);
        })
            ->get()
            ->map
            ->only(['child_id', 'full_name']);
        $collection = collect($getchild);
        $child = $collection->pluck('full_name', 'child_id') ? $collection->pluck('full_name', 'child_id') : 0 / null;
        return $child;
    }


}
