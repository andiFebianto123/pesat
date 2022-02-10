<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ConfigRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ConfigCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ConfigCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Config::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/general');
        CRUD::setEntityNameStrings('General', 'General');
    }

    protected function setupShowOperation(){
        $this->crud->set('show.setFromDb', false);
        $this->crud->addColumns([
            [
                'name' => 'key',
                'label' => 'Key',
            ],

            [
                'name' => 'value',
                'label' => 'Value',
                'limit' => 255,
                'type' => 'closure',
                'function' => function($entry){
                    return '<span class="text-wrap">' . $entry->value . '</br>
                    <small class="font-italic">Alamat email ini hanya untuk kebutuhan administrasi (pendaftaran member, order sponsor baru). Jika merubah email ini maka email selanjutnya akan menggunakan ini.</small></span>';
                },
                'orderable' => false,
                'searchLogic' => false
            ],

        ]);
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
                'name' => 'key',
                'label' => 'Key',
            ],

            [
                'name' => 'value',
                'label' => 'Value',
                'limit' => 255,
                'type' => 'closure',
                'function' => function($entry){
                    return '<span class="text-wrap">' . $entry->value . '</br>
                    <small class="font-italic">Alamat email ini hanya untuk kebutuhan administrasi (pendaftaran member, order sponsor baru). Jika merubah email ini maka email selanjutnya akan menggunakan ini.</small></span>';
                },
                'orderable' => false,
                'searchLogic' => false
            ],

        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(ConfigRequest::class);
        $this->crud->addFields([

            [
                'name' => 'key',
                'label' => "Key",
                'attributes' => [
                    'readonly' => true
                ]
            ],
            [
                'name' => 'value',
                'label' => "Value",
                'hint' => ' <span class="font-italic">Alamat email ini hanya untuk kebutuhan administrasi (pendaftaran member, order sponsor baru). Jika merubah email ini maka email selanjutnya akan menggunakan ini.</span>'
            ],
        ]);
    }
}
