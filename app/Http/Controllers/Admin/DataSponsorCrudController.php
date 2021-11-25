<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DataSponsorRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DataSponsorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DataSponsorCrudController extends CrudController
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
        CRUD::setModel(\App\Models\DataSponsor::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/data-sponsor');
        CRUD::setEntityNameStrings('Data Sponsor', 'Data Sponsor');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
//        CRUD::setFromDb(); 
        $this->crud->removeButton('show');
        $this->crud->removeButton('create');
        $this->crud->removeButton('update');
        $this->crud->addButtonFromModelFunction('line', 'edit', 'EditUser', 'beginning');

        $this->crud->addClause('where', 'user_role_id', '=',4);

        $this->crud->addColumns([
            [
                'name'=>'full_name',
                'label'=>'Nama'
            ],
                
            [
                'label'=>'Gereja',
                'name'=>'church_member_of',
            ],
            [
                'label'=>'Kota',
                'name'=>'address',
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
        CRUD::setValidation(DataSponsorRequest::class);
        
        $randforpass = Str::random(10);
        $encryppass  = bcrypt($randforpass); 
        $username               = [
            'name' => 'name',
            'type' => 'text',
            'label' => "username",
            ];
        $email                  = [
                'name' => 'email',
                'type' => 'text',
                'label' => "Email",
                ];
        $firstname              = [
                'name' => 'first_name',
                'type' => 'text',
                'label' => "Nama Depan",
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                      ]
                ];
        $lastname              = [
                'name' => 'last_name',
                'type' => 'text',
                'label' => "Nama Belakang",
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                      ]
                    ];
        $fullname              = [
                'name' => 'full_name',
                'type' => 'text',
                'label' => "Nama Lengkap",
                ];
        $role                   = [
            'name'        => 'user_role_id',
            'label'       => "Role",
            'type'        => 'select2_from_array',
            'allows_null' => false,
            'options'     => $this->userrole(),
            ];                   
        $password          = [   
                'name'      => 'pass',
                'label'     => 'Password',
                'type'      => 'text',
                'default'   => $randforpass,
                'attributes'=>[
                  'disabled'=>true
                ]

        ];
        $passwordvalue               =[
                'name' => 'password',
                'type' => 'hidden',
                'label' => "Password",
                'default'=> $encryppass,
                ];

        $hometown              = [
                'name' => 'hometown',
                'type' => 'text',
                'label' => "Tempat Lahir",
                'wrapperAttributes' => [
                            'class' => 'form-group col-md-6'
                     ]
                ];

        $dateofbirth        =[   // date_picker
                'name'  => 'date_of_birth',
                'type'  => 'date_picker',
                'label' => 'Tanggal Lahir',
                'wrapperAttributes' => [
                     'class' => 'form-group col-md-6'
                ],

                'date_picker_options' => [
                'todayBtn' => 'linked',
                'format'   => 'dd-mm-yyyy',
                'language' => 'en'
                    ],
                ];
        $address                = [
                'name' => 'address',
                'type' => 'text',
                'label' => "Tempat Tinggal",
                ];
        $noHP                  = [
                'name' => 'no_hp',
                'type' => 'text',
                'label' => "No Hp",
            ];
        $churchmemberof        = [
                'name' => 'church_member_of',
                'type' => 'text',
                'label' => "Jemaat Dari Gereja",

                ];
        $label              = [   
                    'name'  => 'separator',
                    'type'  => 'custom_html',
                    'value' => '<h4>Contact Info</h4>'
                ];

        $website                   = [
                    'label'  => "Website",
                    'name'   => "website_url",
                    'type'   => 'text',
        ];
        $facebook                   = [
                    'label'  => "Facebook",
                    'name'   => "facebook_url",
                    'type'   => 'text',
        ];
        $instagram                   = [
                    'label'  => "Instagram",
                    'name'   => "instagram_url",
                    'type'   => 'text',
        ];
        $linkedin                    = [
                    'label'  => "Linkedin",
                    'name'   => "linkedin_url",
                    'type'   => 'text',
        ];
        $myspace                    = [
                    'label'  => "MySpace",
                    'name'   => "my_space_url",
                    'type'   => 'text',
        ];
        $pinterest                    = [
                    'label'  => "Pinterest",
                    'name'   => "pinterest_url",
                    'type'   => 'text',
        ];
        $soundcloud                    = [
                    'label'  => "SoundCloud",
                    'name'   => "sound_cloud_url",
                    'type'   => 'text',
        ];
        $tumblr                        = [
                    'label'  => "Tumblr",
                    'name'   => "tumblr_url",
                    'type'   => 'text',
        ];
        $twitter                    = [
                    'label'  => "Twitter",
                    'name'   => "twitter_url",
                    'type'   => 'text',
        ];
        $youtube                    = [
                    'label'  => "Youtube",
                    'name'   => "youtube_url",
                    'type'   => 'text',
        ];
        $biograpical                 = [
                    'label'  => "Biograpical",
                    'name'   => "biograpical",
                    'type'   => 'textarea',
        ];
        $photo                    = [
                    'label' => "Photo Profile",
                    'name' => "photo_profile",
                    'type' => 'image',
                    'crop' => true, // set to true to allow cropping, false to disable
                    'disks'  => 'image',
                    'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ];

        $this->crud->addFields([$username,$email,$firstname,$lastname,
                                $fullname,$role,$password,$passwordvalue,
                                $hometown,$dateofbirth,$address,$noHP,
                                $churchmemberof,$label,$website,$facebook,
                                $instagram,$linkedin,$myspace,$pinterest,
                                $soundcloud,$tumblr,$twitter,$youtube,
                                $biograpical,$photo

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
