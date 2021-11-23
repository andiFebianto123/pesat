<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DataOrderProjectRequest;
use App\Models\OrderProject;
use App\Models\ProjectMaster;
use App\Models\Sponsor;
use App\Services\Midtrans\CreateSnapTokenForProjectService;
use App\Services\Midtrans\CreateSnapTokenService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\DB;

/**
 * Class DataOrderProjectCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DataOrderProjectCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {store as traitstore;}
//    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\DataOrderProject::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/data-order-project');
        CRUD::setEntityNameStrings('data order project', 'data order projects');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addButtonFromModelFunction('line', 'cekstatus', 'Cek_Status', 'last');
        $this->crud->addColumns([

            [
                'name' => 'order_project_id',
                'label' => 'Order ID',
                'type' => 'text',
              
            ],
            [
                'name' => 'sponsor_id',
                'label' => 'Sponsor',
                'entity' => 'sponsorname',
                'attribute' => 'full_name',
              
            ],
            [
                'name' => 'project_id',
                'label' => 'Nama Proyek',
                'entity' => 'projectname',
                'attribute' => 'title',
              
            ],
            [
                'name' => 'price',
                'label' => 'Nominal Donasi',
                'prefix'=> 'Rp. '
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
        CRUD::setValidation(DataOrderProjectRequest::class);


        $sponsor = [
            'name' => 'sponsor_id',
            'label' => "Nama Sponsor",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->sponsor(),

        ];

        $project = [
            'name' => 'project_id',
            'label' => "Nama Proyek",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->project(),
        ];

        $price=[
            'name'  => 'price',
            'type'  => 'text'
        ];



        $this->crud->addFields([
            $sponsor,$project,
            $price
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
        $item = $this->crud->create($this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        $Snaptokenorder = DB::table('order_project')->where('order_project.order_project_id',$item->order_project_id)
        ->join('sponsor_master as sm','sm.sponsor_id','=','order_project.sponsor_id')
        ->join('project_master as pm','pm.project_id','=','order_project.project_id')
        ->select(
            'order_project.*', 
            'pm.title',
            'sm.full_name',
            'sm.email',
            'sm.no_hp'
            )
        ->get();

        $order = OrderProject::where('order_project_id',$item->order_project_id)->first();                        
        $midtrans = new CreateSnapTokenForProjectService($Snaptokenorder,$item->order_project_id);
        $snapToken = $midtrans->getSnapToken();
        $order->snap_token = $snapToken;
        $order->save();
        
        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    public function project(){

        $getproject = ProjectMaster::get();
        $collection = collect($getproject);
        $project = $collection->pluck('title', 'project_id') ? $collection->pluck('title', 'project_id') : 0 / null;
        return $project;
    }
    public function sponsor(){

        $getsponsor = Sponsor::get();
        $collection = collect($getsponsor);
        $sponsor = $collection->pluck('full_name', 'sponsor_id') ? $collection->pluck('full_name', 'sponsor_id') : 0 / null;
        return $sponsor;
    }
}
