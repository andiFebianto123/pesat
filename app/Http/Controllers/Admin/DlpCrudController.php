<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DlpRequest;
use App\Models\ChildMaster;
use App\Models\Dlp;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

/**
 * Class DlpCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DlpCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Dlp::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/dlp');
        CRUD::setEntityNameStrings('dlp', 'DLP');

        $this->crud->child_id = \Route::current()->parameter('header_id');

        Dlp::addGlobalScope('header_id', function (Builder $builder) {
            $builder->where('child_id', $this->crud->child_id);
        });
        CRUD::setModel(Dlp::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/dlp/' . ($this->crud->child_id ?? '-') . '/detail');
        CRUD::setEntityNameStrings('Add DLP', 'Add DLP');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    public function getChildMaster($id){
      
        $cekdata = ChildMaster::where('child_id', $id);
        $cekdata =  $cekdata->first();
        if($cekdata == null){
            DB::rollback();
            abort(404, trans('Data Not Valid'));
        }
        return $cekdata;
    }

    protected function setupListOperation()
    {
        $this->crud->cekdata = $this->getChildMaster($this->crud->child_id);

        $this->crud->addButtonFromModelFunction('line', 'sendmail', 'Send_Email', 'beginning');

        CRUD::addColumns([

            [
                'name' => 'file_dlp',
                'label' => 'Nama Profile',
                'type' => 'custom_html',
                'value' => '<span>File</span>',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $related_key) {
                        return url('storage/' . $entry->file_dlp);

                    },

                    'target' => '__blank',
                ],

            ],
            [   // Enum
                'name'  => 'deliv_status',
                'label' => 'Status',
                'type' => 'radio',
                'options' => [1 => 'Belum Dikirim', 2 => 'Sukses',3=> 'Gagal'],
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
        CRUD::setValidation(DlpRequest::class);

        $childid = $this->crud->child_id;
        $filepdlp = [
            'label' => "File DLP",
            'name' => "file_dlp",
            'type' => 'upload',
            'upload' => true,
            'crop' => false,
            'disks' => 'uploads_dlp',
            'prefix' => '/storage/',
        ];
        $childid = [
            'name' => 'child_id',
            'type' => 'hidden',
            'default' => $childid,
            'label' => "id",
        ];
        $this->crud->addFields([$childid, $filepdlp]);

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
}
