<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ChildMasterRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ChildMasterCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ChildMasterCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ChildMaster::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/child-master');
        CRUD::setEntityNameStrings('child master', 'Data Anak');
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
       $this->crud->addButtonFromModelFunction('line', 'open_dlp', 'DetailDlp', 'beginning');
        //$this->crud->addButtonFromModelFunction('top', 'email', 'TestEmail', 'beginning');
      //  $this->crud->addButtonFromView('top', 'sendmail', 'test', 'beginning');
        $this->crud->removeButton('delete');
        $this->crud->addColumns([
            [
                'name'=>'full_name',
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
            [
                'name' => 'DLP',
                'type'      => 'boolean',
                'options' => [0 => 'Not Sent', 1 => 'Sent']
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
        CRUD::setValidation(ChildMasterRequest::class);

        //CRUD::setFromDb();
        $userid = backpack_user()->id;

        $createdby          = [
                                'name' => 'created_by',
                                'type' => 'hidden',
                                'label' => "Nama Lengkap",
                                'default'=> $userid
                              ];
        $name               = [
                                'name' => 'full_name',
                                'type' => 'text',
                                'label' => "Nama Lengkap",
                                'attributes'=>[
                                'required'=>true,
                                ]
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
                             
        $childdiscription   = [
                                'name' => 'child_discription',
                                'label' => 'Deskripsi Anak',
                                'type' => 'ckeditor',
                              ];
        $noRegistration     = [
                                'name'  => 'registration_number',
                                'label' => 'No Induk',
                                'type'  => 'text',
                                'attributes'=>[
                                  'required'=>true,
                                ],
                                'wrapperAttributes' => [
                                  'class' => 'form -grup col-md-6'
                                ]
                              ];
        $nickname           = [
                                'name'  => 'nickname',
                                'label' => 'Nama Panggilan',
                                'type'  => 'text',
                                'attributes'=>[
                                'required'=>true,
                                ],
                                'wrapperAttributes' => [
                                  'class' => 'form -grup col-md-6'
                                ]
                              ];   
        $gender           =   [
                                'name'  => 'gender',
                                'label' => 'Jenis Kelamin',
                                'type'  => 'text',
                                'attributes'=>[
                                'required'=>true,
                                ]
                              ];
        $hometown           =   [
                                'label' => "Tempat Lahir",
                                'type' => 'select',
                                'name' => 'city', // the method that defines the relationship in your Model// optional
                                'entity' => 'city', // the method that defines the relationship in your Model
                                'model' => "App\Models\City", // foreign key model
                                'attribute' => 'city_name', // foreign key attribute that is shown to user
                                'pivot' => false, // on create&update, do you need to add/delete pivot table entries?
                                'wrapperAttributes' => [
                                  'class' => 'form -grup col-md-6'
                                ]
                              ];  
        $dateofbirth        = [
                                'name'  => 'date_of_birth',
                                'label' => 'Tanggal Lahir',
                                'type'  => 'date',
                                'wrapperAttributes' => [
                                'class' => 'form -grup col-md-6'
                            ]
                              ];
        $religion           = [
                                'label' => "Agama",
                                'type' => 'select',
                                'name' => 'religion', // the method that defines the relationship in your Model// optional
                                'entity' => 'religion', // the method that defines the relationship in your Model
                                'model' => "App\Models\Religion", // foreign key model
                                'attribute' => 'religion_name', // foreign key attribute that is shown to user
                                'pivot' => false, // on create&update, do you need to add/delete pivot table entries?
                              ];
        $FC                 = [
                                'name'  => 'fc',
                                'label' => 'FC',
                                'type'  => 'text'
                              ];
        $sponsor              = [
                                'name'  => 'sponsor_name',
                                'label' => 'Sponsor',
                                'type'  => 'text'
                              ];
       
        $districts            = [
                                'name'  => 'districts',
                                'label' => 'Kecamatan',
                                'type'  => 'text'
                              ];
        $province             = [   // 1-n relationship
                                'label'       => "Propinsi", // Table column heading
                                'type'        => "select2_from_ajax",
                                'name'        => 'province_id', // the column that contains the ID of that connected entity
                                'entity'      => 'province', // the method that defines the relationship in your Model
                                'attribute'   => "province_name", // foreign key attribute that is shown to user
                                'data_source' => url("api/provice"), // url to controller search function (with /{id} should return model)
                                'placeholder'             => "Select a Province", // placeholder for the select
                                'minimum_input_length'    => 2, // minimum characters to type before querying results
                                'wrapperAttributes' => [
                                'class' => 'form -grup col-md-6'
                                ]
                                ];
        $city             =   [   // 1-n relationship
                                'label'       => "Kabupaten", // Table column heading
                                'type'        => "select2_from_ajax",
                                'name'        => 'city_id', // the column that contains the ID of that connected entity
                                'entity'      => 'city2', // the method that defines the relationship in your Model
                                'attribute'   => "city_name", // foreign key attribute that is shown to user
                                'data_source' => url("api/city"), // url to controller search function (with /{id} should return model)
                                'placeholder'             => "Select a City", // placeholder for the select
                                'minimum_input_length'    => 2, // minimum characters to type before querying results
                                'wrapperAttributes' => [
                                'class' => 'form -grup col-md-6'
                                ]
                              ];

        $father             = [
                                'name'  => 'father',
                                'label' => 'Ayah',
                                'type'  => 'text',
                                'wrapperAttributes' => [
                                  'class' => 'form -grup col-md-6'
                                  ]
                              ];
        $mother             = [
                                'name'  => 'mother',
                                'label' => 'Ibu',
                                'type'  => 'text',
                                'wrapperAttributes' => [
                                  'class' => 'form -grup col-md-6'
                                  ]
                              ];
        $profession         = [
                                'name'  => 'profession',
                                'label' => 'Pekerjaan',
                                'type'  => 'text'
                              ];
        $economy            = [
                                'name'  => 'economy',
                                'label' => 'Ekonomi',
                                'type'  => 'text'
                              ];
        $class              = [
                                'name'  => 'class',
                                'label' => 'Kelas',
                                'type'  => 'text',
                                'wrapperAttributes' => [
                                'class' => 'form -grup col-md-4'
                                  ]
                              ];
        $school             = [
                                'name'  => 'school',
                                'label' => 'Sekolah',
                                'type'  => 'text',
                                'wrapperAttributes' => [
                                'class' => 'form -grup col-md-4'
                                  ]
                              ];
        $schoolyear         = [
                                'name'  => 'school_year',
                                'label' => 'Tahun Ajaran',
                                'type'  => 'text',
                                'wrapperAttributes' => [
                                'class' => 'form -grup col-md-4'
                                  ]
                              ];
        $signinfc           = [
                                'name'  => 'sign_in_fc',
                                'label' => 'Masuk FC',
                                'type'  => 'date',
                                'wrapperAttributes' => [
                                'class' => 'form -grup col-md-6'
                                  ]
                              ];
        $leavefc            = [
                                'name'  => 'leave_fc',
                                'label' => 'Keluar FC',
                                'type'  => 'date',
                                'wrapperAttributes' => [
                                'class' => 'form -grup col-md-6'
                                  ]
                              ];
        $reasontoleave      = [
                                'name'  => 'reason_to_leave',
                                'label' => 'Alasan Keluar',
                                'type'  => 'textarea'
                              ];
        
        $internaldiscription = [
                                'name'  => 'internal_discription',
                                'label' => 'Keterangan Internal',
                                'type'  => 'textarea'
                              ]; 
        
        $photo               = [
                                'label' => "Profile Image",
                                'name' => "photo_profile",
                                'type' => 'image',
                                'crop' => true, // set to true to allow cropping, false to disable
                                'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
                               ];
        
        $fileprofile         = [
                                'label'  => "File Profile",
                                'name'   => "file_profile",
                                'type'   => 'upload',
                                'upload' => true,
                                'crop'   => false,
                                'disks'  => 'uploads',
                                'prefix' => '/storage/'
                              ];
        
        $this->crud->addFields([$createdby,$name,$sponsor_type,$childdiscription, 
                                $noRegistration,$nickname,$gender,$hometown,
                                $dateofbirth,$religion,$FC,$sponsor,$province,
                                $city,$districts, $father,$mother,
                                $profession,$economy,$class,$school,
                                $schoolyear,$signinfc,$leavefc,$reasontoleave,
                                $internaldiscription,$photo,$fileprofile
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
}
