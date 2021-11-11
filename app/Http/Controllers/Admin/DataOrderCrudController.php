<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DataOrderRequest;
use App\Models\DataDetailOrder;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DataOrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DataOrderCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    //use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
 //   use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {destroy as traitDestroy;}

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
        
        $this->crud->addColumns([
            [
                'name' => 'order_no',
                'label' => 'No Order',
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
}
