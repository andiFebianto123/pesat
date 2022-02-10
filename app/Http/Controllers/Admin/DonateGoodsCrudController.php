<?php

namespace App\Http\Controllers\Admin;

use App\Models\DonateGoods;
use App\Http\Requests\DonateGoodsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DonateGoodsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DonateGoodsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\DonateGoods::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/donate-goods');
        CRUD::setEntityNameStrings('Donasi Barang', 'Donasi Barang');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        CRUD::addColumns([

            [
                'name' => 'title',
                'label' => 'Title',
                'type' => 'text',
            ],
        ]);
        $countGoods = DonateGoods::all()->count();
        if ($countGoods >= 1) {
            $this->crud->denyAccess('create');
        }
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DonateGoodsRequest::class);

        $title = [
            'name' => 'title',
            'label' => 'Judul',
            'type' => 'text',
        ];
        $discription = [
            'name' => 'discription',
            'label' => 'Deskripsi',
            'type' => 'ckeditor',
        ];

        $this->crud->addFields([
            $title, $discription
        ]);
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

    function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        $this->crud->allowAccess('create');
        return $this->crud->delete($id);
    }
}
