<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CityRequest;
use App\Models\ChildMaster;
use App\Models\City;
use App\Models\Province;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CityCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CityCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation {show as traitshow;}

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\City::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/city');
        CRUD::setEntityNameStrings('Kota', 'Kota / Kabupaten');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
       // CRUD::setFromDb();
       $this->crud->addColumns([
        [
            'name'=>'province_id',
            'label'=>'Nama Province',
            'entity'=>'province',
            'attribute'=>'province_name'
        ],
        [
            'name'=>'city_name',
            'label'=>'Nama Kota'
        ]
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
        CRUD::setValidation(CityRequest::class);

        //CRUD::setFromDb();
        $provinces       =     [
            'name'        => 'province_id',
            'label'       => "Provinsi",
            'type'        => 'select2_from_array',
            'allows_null' => false,
            'options'     => $this->province(),

          ];
        $cityname          = [
            'name' => 'city_name',
            'type' => 'text',
            'label' => "Nama Kota",
            
          ];

          $this->crud->addFields([
            $provinces,$cityname
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

    public function province()
    {
        $province = Province::get()
            ->map
            ->only(['province_id', 'province_name']);
        $collection=collect($province);
        $province = $collection->pluck('province_name','province_id') ? $collection->pluck('province_name','province_id') : 0/null;
        return $province;
    }
    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        

        $cekhometown = ChildMaster::where('hometown',$id);
        $cekcity     = ChildMaster::where('city_id',$id);
        $hometown    = $cekhometown->exists();
        $city        = $cekcity->exists();
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        if($hometown == true || $city == true){
            return response()->json(array('status'=>'error', 'msg'=>'Error!','message'=>'The selected data has already had relation with other data.'), 403);
        }else{
            return $this->crud->delete($id);

        }
    }
    function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->addColumns([
            [
                'name'  => 'province_id',
                'label' => 'Nama Provinsi',
                'entity'=> 'province',
                'attribute' =>'province_name'
            ],
            [
                'name' => 'city_name',
                'label' => 'Nama Provinsi',
            ]
        ]);
    }
}
