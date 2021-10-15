<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UsersRequest;
use App\Models\User;
use App\Models\UserAttribute;
use App\Models\Users;
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
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {edit as traitedit;}


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
                'value'=>"user_role_id",
                'attribute' => 'user_role_name', // foreign key attribute that is shown to user
                'pivot' => false, // on create&update, do you need to add/delete pivot table entries?
                  ];
        $password          = [   
                'name'      => 'pass',
                'label'     => 'Password',
                'type'      => 'text',
                'default'   => $randforpass,
                'attributes'=>[
                     'disabled'=>true,
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
        $filepdlp         = [
            'label'  => "File DLP",
            'name'   => "file_dlp",
            'type'   => 'upload',
            'upload' => true,
            'crop'   => false,
            'disks'  => 'uploads_dlp',
            'prefix' => '/storage/'
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
    //$x=$request->input('photo_profile');
    //dd($x);
    $item = $this->crud->create($this->crud->getStrippedSaveRequest());
    $this->data['entry'] = $this->crud->entry = $item;
 

     $lastUserdId = User::latest()->pluck('id')->first(); //get lastest child id
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




    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $userAttribute = UserAttribute::where('user_id',$id)->first();
        
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
                    'default'=> $userAttribute->website_url,
                    'type'   => 'text',
        ];
        $facebook                   = [
                    'label'  => "Facebook",
                    'name'   => "facebook_url",
                    'default'=> $userAttribute->facebook_url,
                    'type'   => 'text',
        ];
        $instagram                   = [
                    'label'  => "Instagram",
                    'name'   => "instagram_url",
                    'default'=> $userAttribute->instagram_url,
                    'type'   => 'text',
        ];
        $linkedin                    = [
            'label'  => "Linkedin",
            'name'   => "linkedin_url",
            'default'=> $userAttribute->linkedin_url,
            'type'   => 'text',
        ];
        $myspace                    = [
            'label'  => "MySpace",
            'name'   => "my_space_url",
            'default'=> $userAttribute->my_space_url,
            'type'   => 'text',
        ];
        $pinterest                    = [
            'label'  => "Pinterest",
            'name'   => "pinterest_url",
            'default'=> $userAttribute->pinterest_url,
            'type'   => 'text',
        ];
        $soundcloud                    = [
            'label'  => "SoundCloud",
            'name'   => "sound_cloud_url",
            'default'=> $userAttribute->sound_cloud_url,
            'type'   => 'text',
        ];
        $tumblr                        = [
            'label'  => "Tumblr",
            'name'   => "tumblr_url",
            'default'=> $userAttribute->tumblr_url,
            'type'   => 'text',
        ];
        $twitter                    = [
            'label'  => "Twitter",
            'name'   => "twitter_url",
            'default'=> $userAttribute->twitter_url,
            'type'   => 'text',
        ];
        $youtube                    = [
            'label'  => "Youtube",
            'name'   => "youtube_url",
            'default'=> $userAttribute->youtube_url,
            'type'   => 'text',
        ];
        $biograpical                 = [
             'label'  => "Biograpical",
             'name'   => "biograpical",
             'default'=> $userAttribute->biograpical,
             'type'   => 'text',
         ];

        $photo                    = [
            'label' => "Photo Profile",
            'name' => "photo_profile",
            'default'=> $userAttribute->photo_profile,
            'type' => 'image',
            'crop' => true, // set to true to allow cropping, false to 
            'prefix'=>'/storage/',
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ];
        $this->crud->addFields([$username,$email,$firstname,$lastname,
                                $fullname,$role,
                                $hometown,$dateofbirth,$address,$noHP,
                                $churchmemberof,$label,$website,$facebook,
                                $instagram,$linkedin,$myspace,$pinterest,
                                $soundcloud,$tumblr,$twitter,$youtube,
                                $biograpical,$photo

        ]);
        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());
        
        // get the info for that entry

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit').' '.$this->crud->entity_name;

        $this->data['id'] = $id;


        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getEditView(), $this->data);
    }
    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db
        
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
                            $this->crud->getStrippedSaveRequest());
                           
        $this->data['entry'] = $this->crud->entry = $item;
        
         UserAttribute::where('user_id', $item->id)
        ->update(['website_url' => $request->input('website_url'),
                  'facebook_url'=> $request->input('facebook_url'),
                  'instagram_url'=> $request->input('instagram_url'),
                  'my_space_url'=> $request->input('my_space_url'),
                  'pinterest_url'=> $request->input('pinterest_url'),
                  'sound_cloud_url'=> $request->input('sound_cloud_url'),
                  'tumblr_url'=> $request->input('tumblr_url'),
                  'twitter_url'=> $request->input('twitter_url'),
                  'youtube_url'=> $request->input('youtube_url'),
                  'biograpical'=> $request->input('biograpical'),
                  'photo_profile'=> $request->input('photo_profile')
                
                ]);
        
        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
   // protected function setupUpdateOperation()
   // {

    //}
}
