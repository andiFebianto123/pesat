<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProvinceRequest;
use App\Http\Requests\ProvinceUpdateRequest as UpdateRequest;
use App\Models\ChildMaster;
use App\Models\City;
use App\Models\Province;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProvinceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProvinceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {destroy as traitDestroy;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation {show as traitshow;}
    //   use RedirectCrud;
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    function setup()
    {
        CRUD::setModel(\App\Models\Province::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/province');
        CRUD::setEntityNameStrings('Provinsi', 'Provinsi');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    function setupListOperation()
    {

        $this->crud->addColumns([
            [
                'name' => 'province_name',
                'type' => 'text',
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

        CRUD::setValidation(ProvinceRequest::class);

        $provincename = [
            'name' => 'province_name',
            'type' => 'text',
            'label' => "Nama Provinsi",

        ];

        $this->crud->addFields([
            $provincename,
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
//        $this->setupCreateOperation();
        CRUD::setValidation(UpdateRequest::class);
        $provincename = [
            'name' => 'province_name',
            'type' => 'text',
            'label' => "Nama Provinsi",
        ];

        $this->crud->addFields([$provincename,

        ]);
    }

    function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // return $this->redirectStoreCrud(['province_name'=>['abc']]);

        // insert item in the db
        $item = $this->crud->create($this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $cekcity = City::where('province_id', $id);
        $city = $cekcity->exists();

        $cekchild = ChildMaster::where('province_id', $id);
        $child = $cekchild->exists();
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        if ($city == true || $child == true) {
            return response()->json(array('status' => 'error', 'msg' => 'Error!', 'message' => 'The selected data has already had relation with other data.'), 403);
        } else {
            return $this->crud->delete($id);

        }
    }

    function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->addColumns([
            [
                'name' => 'province_name',
                'label' => 'Nama Provinsi',
            ],
        ]);
    }
}
