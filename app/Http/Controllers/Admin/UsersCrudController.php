<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UsersRequest;
use App\Models\User;
use App\Models\UserAttribute;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Str;

/**
 * Class UsersCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UsersCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {store as traitstore;}

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Users::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/users');
        CRUD::setEntityNameStrings('users', 'users');
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
        $this->crud->addColumns([
            [
                'name'=>'name',
                'label'=>'Username'
            ],
            [
                'name'=>'email',
                'label'=>'Email'
            ],
            [
                'type' => 'relationship',
                'name' => 'role', // the relationship name in your Model
                'label' => 'Role',
                'entity' => 'role', // the relationship name in your Model
                'attribute' => 'user_role_name', // attribute on Article that is shown to admin
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
        CRUD::setValidation(UsersRequest::class);

        //CRUD::setFromDb();
        $randforpass = Str::random(10);
        $encryppass  = bcrypt($randforpass); 
        $username               = [
            'name' => 'name',
            'type' => 'text',
            'label' => "username",
            'attributes'=>[
            'required'=>true,
            ]
            ];
        $email                  = [
                'name' => 'email',
                'type' => 'text',
                'label' => "Email",
                'attributes'=>[
                'required'=>true,
                    ]
                ];
        $firstname              = [
                'name' => 'first_name',
                'type' => 'text',
                'label' => "First Name",
                'wrapperAttributes' => [
                    'class' => 'form -grup col-md-6'
                      ]
                ];
        $lastname              = [
                'name' => 'last_name',
                'type' => 'text',
                'label' => "Last Name",
                'wrapperAttributes' => [
                    'class' => 'form -grup col-md-6'
                      ]
                    ];
        $fullname              = [
                'name' => 'full_name',
                'type' => 'text',
                'label' => "Full Name",
                'attributes'=>[
                            'required'=>true,
                        ]
                ];
        $role                   = [ // Select2Multiple = n-n relationship (with pivot table)
                'label' => "Role",
                'type' => 'select',
                'name' => 'role', // the method that defines the relationship in your Model// optional
                'entity' => 'role', // the method that defines the relationship in your Model
                'model' => "App\Models\UserRole", // foreign key model
                'attribute' => 'user_role_name', // foreign key attribute that is shown to user
                'pivot' => false, // on create&update, do you need to add/delete pivot table entries?
                  ];
        $password          = [   
                'name'      => 'pass',
                'label'     => 'Password',
                'type'      => 'text',
                'default'   => $randforpass,
                // 'attributes'=>[
                //     'disabled'=>true,
                //     ]
        ];
        $passwordvalue               =[
                'name' => 'password',
                'type' => 'hidden',
                'label' => "Password",
                'default'=> $encryppass,
               // 'value' => $encryppass,
               //  'attributes'=>[
               //  'disabled'=>true,
               //  ]
                ];

        $hometown              = [
                'name' => 'hometown',
                'type' => 'text',
                'label' => "Tempat Lahir",
                'attributes'=>[
                            'required'=>true,
                ],
                'wrapperAttributes' => [
                            'class' => 'form -grup col-md-6'
                     ]
                ];
        $dateofbirth            = [
                'name' => 'date_of_birth',
                'type' => 'date',
                'label' => "Tanggal Lahir",
                'attributes'=>[
                                'required'=>true,
                ],
                'wrapperAttributes' => [
                    'class' => 'form -grup col-md-6'
                      ]
                ];
        $address                = [
                'name' => 'date_of_birth',
                'type' => 'text',
                'label' => "Tanggal Lahir",
                'attributes'=>[
                            'required'=>true,
                            ]
                ];
        $noHP                  = [
                'name' => 'no_hp',
                'type' => 'text',
                'label' => "No Hp",
                'attributes'=>[
                            'required'=>true,
                        ]
            ];
        $churchmemberof        = [
                'name' => 'church_member_of',
                'type' => 'text',
                'label' => "Jemaat Dari Gereja",
                'attributes'=>[
                            'required'=>true,
                            ]
                ];
        $label                  = [   
                    'name'  => 'separator',
                    'type'  => 'custom_html',
                    'value' => '<h4>Contact Info</h4>'
                ];

        $website                   = [
                    'label'  => "Website",
                    'name'   => "website_url",
                    'entity' => 'role',
                    'type'   => 'text',
        ];
        $facebook                   = [
                    'label'  => "Facebook",
                    'name'   => "facebook_url",
                    'entity' => 'role',
                    'type'   => 'text',
        ];
        $instagram                   = [
                    'label'  => "Instagram",
                    'name'   => "instagram_url",
                    'entity' => 'role',
                    'type'   => 'text',
        ];
        $linkedin                    = [
            'label'  => "Linkedin",
            'name'   => "linkedin_url",
            'entity' => 'role',
            'type'   => 'text',
        ];
        $myspace                    = [
            'label'  => "MySpace",
            'name'   => "my_space_url",
            'entity' => 'role',
            'type'   => 'text',
        ];
        $pinterest                    = [
            'label'  => "Pinterest",
            'name'   => "pinterest_url",
            'entity' => 'role',
            'type'   => 'text',
        ];
        $soundcloud                    = [
            'label'  => "SoundCloud",
            'name'   => "sound_cloud_url",
            'entity' => 'role',
            'type'   => 'text',
        ];
        $tumblr                        = [
            'label'  => "Tumblr",
            'name'   => "tumblr_url",
            'entity' => 'role',
            'type'   => 'text',
        ];
        $twitter                    = [
            'label'  => "Twitter",
            'name'   => "twitter_url",
            'entity' => 'role',
            'type'   => 'text',
        ];
        $youtube                    = [
            'label'  => "Youtube",
            'name'   => "youtube_url",
            'entity' => 'role',
            'type'   => 'text',
        ];
        $biograpical                 = [
            'label'  => "Biograpical",
            'name'   => "biograpical",
            'entity' => 'role',
            'type'   => 'textarea',
        ];
        $photo                    = [
            'label' => "Photo Profile",
            'name' => "photo_profile",
            'type' => 'image',
            'crop' => true, // set to true to allow cropping, false to disable
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
    public function store()
    {
    $this->crud->hasAccessOrFail('create');
    // execute the FormRequest authorization and validation, if one is required 
    $request = $this->crud->validateRequest();
    // insert item in the db
    $item = $this->crud->create($this->crud->getStrippedSaveRequest());
    $this->data['entry'] = $this->crud->entry = $item;
 
     //$list = $request->input('file_dlp'); // $list
     $lastUserdId = User::latest()->pluck('id')->first(); //get lastest child id
    // $listdata = json_decode($list,true);
     $userattribute = new UserAttribute();
     $userattribute->user_id       = $lastUserdId;
     $userattribute->website_url   = $request->input('website_url');
     $userattribute->facebook_url  = $request->input('facebook_url');
     $userattribute->instagram_url = $request->input('instagram_url');
     $userattribute->linkedin_url  = $request->input('linkedin_url');
     $userattribute->my_space_url  = $request->input('my_space_url');
     $userattribute->pinterest_url = $request->input('pinterest_url');
     $userattribute->tumblr_url    = $request->input('tumblr_url');
     $userattribute->twitter_url   = $request->input('twitter_url');
     $userattribute->youtube_url   = $request->input('youtube_url');
     $userattribute->biograpical   = $request->input('biograpical');
     $userattribute->photo_profile = $request->input('photo_profile');
        
         
          $userattribute->save();
  //     }
  // }
    // show a success message
    \Alert::success(trans('backpack::crud.insert_success'))->flash();
    // save the redirect choice for next time
    $this->crud->setSaveAction();
    return $this->crud->performSaveAction($item->getKey());
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
