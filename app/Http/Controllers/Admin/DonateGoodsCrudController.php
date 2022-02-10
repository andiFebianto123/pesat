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

        $countGoods = DonateGoods::count();
        if ($countGoods >= 1) {
            $this->crud->denyAccess('create');
        }
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

        CRUD::button('delete')->remove();

        $this->crud->addButtonFromView('line', 'delete_donation_goods', 'delete_donation_goods', 'ending');
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

    function create()
    {
        $this->crud->hasAccessOrFail('create');

        $countGoods = DonateGoods::count();
        if ($countGoods >= 1) {
            \Alert::error(trans('Sudah terdapat donasi barang.'))->flash();
            return redirect(url($this->crud->route));
        }
        $fields = $this->crud->getCreateFields();
        $this->crud->setOperationSetting('fields', $fields);

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();

        return view($this->crud->getCreateView(), $this->data);
    }
}
