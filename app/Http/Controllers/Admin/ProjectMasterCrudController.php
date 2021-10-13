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
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ProjectMaster::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project-master');
        CRUD::setEntityNameStrings('project master', 'project masters');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //CRUD::setFromDb();

        $this->crud->addButtonFromModelFunction('line', 'open_image', 'DetailImage', 'beginning');
        $this->crud->removeButton('delete');
        $this->crud->removeButton('show');
        $this->crud->addColumns([
        [
            'name'=>'title',
            'label'=>'Name'
        ],
        
        [
            'name'=>'status',
            'type'=>'boolean',
            'options'=>[0=>'Not Publish',1=>'Publish']
        ],
         [
             //'type' => 'text',
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
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProjectMasterRequest::class);
        //CRUD::setFromDb();
        $userid = backpack_user()->id;

        $title          = [
            'name' => 'title',
            'type' => 'text',
            'label' => "Nama Proyek",
          ];

        $sponsor_type       = [ // Select2Multiple = n-n relationship (with pivot table)
            'label' => "Type Sponsor",
            'type' => 'select',
            'name' => 'sponsor_type', // the method that defines the relationship in your Model// optional
            'entity' => 'sponsor_type', // the method that defines the relationship in your Model
            'model' => "App\Models\SponsorType", // foreign key model
            'attribute' => 'sponsor_type_name', // foreign key attribute that is shown to user
            'pivot' => false, // on create&update, do you need to add/delete pivot table entries?
            
            //'value' => $this->crud->getCurrentEntryId() ? ChildMaster::find($this->crud->getCurrentEntryId())->sponsor()->where('sponsor_type_id', $this->crud->getCurrentEntryId())->get() : null // <-- Add the default value from the database, with $this->crud->getCurrentEntryId() as the primary key being edited
          ];
        $discription     = [
            'name' => 'discription',
            'type' => 'ckeditor',
            'label' => "Deskripsi",
            'attributes'=>[
            'required'=>true,
            ]
          ];
        $photo               = [
            'label' => "Gambar Unggulan",
            'name' => "featured_image",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            //'disks'  => 'image',
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            'prefix'=>'/storage'
           ];
        $createdby      =[
            'name'=>'created_by',
            'type'=>'hidden',
            'label'=>'id',
            'default'=>$userid
        ];


          $this->crud->addFields([$title,$sponsor_type,$discription,$photo,$createdby]);

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
