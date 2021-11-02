<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProjectMasterRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProjectMasterCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProjectMasterCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation {show as traitshow;}

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    function setup()
    {
        CRUD::setModel(\App\Models\ProjectMaster::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project-master');
        CRUD::setEntityNameStrings('Data Proyek', 'Data Proyek');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    function setupListOperation()
    {

        $this->crud->addButtonFromModelFunction('line', 'open_image', 'DetailImage', 'beginning');
        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'Name',
            ],

            [
                'type' => 'relationship',
                'name' => 'users', // the relationship name in your Model
                'label' => 'Author',
                'entity' => 'users', // the relationship name in your Model
                'attribute' => 'name', // attribute on Article that is shown to admin
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
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
        CRUD::setValidation(ProjectMasterRequest::class);
        $userid = backpack_user()->id;

        $title = [
            'name' => 'title',
            'type' => 'text',
            'label' => "Nama Proyek",
        ];

        $discription = [
            'name' => 'discription',
            'type' => 'ckeditor',
            'label' => "Deskripsi",
            'attributes' => [
                'required' => true,
            ],
        ];
        $photo = [
            'label' => "Gambar Unggulan",
            'name' => "featured_image",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            'prefix' => '/storage',
        ];
        $createdby = [
            'name' => 'created_by',
            'type' => 'hidden',
            'label' => 'id',
            'default' => $userid,
        ];

        $this->crud->addFields([$title, $discription, $photo, $createdby]);

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
        CRUD::setValidation(ProjectMasterRequest::class);

        $title = [
            'name' => 'title',
            'type' => 'text',
            'label' => "Nama Proyek",
        ];

        $discription = [
            'name' => 'discription',
            'type' => 'ckeditor',
            'label' => "Deskripsi",
            'attributes' => [
                'required' => true,
            ],
        ];
        $photo = [
            'label' => "Gambar Unggulan",
            'name' => "featured_image",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            'prefix' => '/storage',
        ];
        $createdby = [
            'name' => 'created_by',
            'type' => 'hidden',
            'label' => 'id',
        ];

        $this->crud->addFields([$title, $discription, $photo, $createdby]);
    }

    function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'Judul',
            ],
            [
                'name' => 'discription',
                'label' => 'Diskripsi',
                'type' => 'text',
                'escaped' => false,

            ],
            [
                'name'     => 'featured_image',
                'label'    => 'Foto',
                'type'     => 'image',
                'prefix'   => 'storage/',
                'height'   => '150px',
                'function' => function($entry) {          
                 return   url($entry->featured_image);
                }
              ],

        ]);
    }
}
