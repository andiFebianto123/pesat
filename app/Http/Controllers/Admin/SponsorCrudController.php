<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Str;
use App\Http\Requests\SponsorRequest;
use App\Models\UserAttribute;
use App\Models\Sponsor;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SponsorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SponsorCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Sponsor::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/sponsor');
        CRUD::setEntityNameStrings('sponsor', 'sponsors');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeButton('show');
        $this->crud->removeButton('update');
        $this->crud->addButtonFromModelFunction('line', 'edit', 'EditSponsor', 'beginning');


        $this->crud->addColumns([
            [
                'name'=>'full_name',
                'label'=>'Name'
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
    public function store()
    {
    $this->crud->hasAccessOrFail('create');
    // execute the FormRequest authorization and validation, if one is required 
    $request = $this->crud->validateRequest();
    // insert item in the db

    $item = $this->crud->create($this->crud->getStrippedSaveRequest());
    $this->data['entry'] = $this->crud->entry = $item;
    

     $lastUserdId = Sponsor::latest()->pluck('sponsor_id')->first(); //get lastest child id
     $userattribute = new UserAttribute();
     $userattribute->sponsor_id       = $lastUserdId;
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
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SponsorRequest::class);

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
                ];
     //   $role                   = [
     //       'name'        => 'user_role_id',
     //       'label'       => "Role",
     //       'type'        => 'select2_from_array',
     //       'allows_null' => false,
     //       'options'     => $this->userrole(),
     //       ];                   
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
                            'class' => 'form -grup col-md-6'
                     ]
                ];

        $dateofbirth        =[   // date_picker
                'name'  => 'date_of_birth',
                'type'  => 'date_picker',
                'label' => 'Tanggal Lahir',
                'wrapperAttributes' => [
                     'class' => 'form -grup col-md-6'
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

        $this->crud->addFields([$username,$email,$firstname,$lastname,
                                $fullname,$password,$passwordvalue,//$role,
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
//    protected function setupUpdateOperation()
//    {
//        $this->setupCreateOperation();
//    }
public function edit($id)
{
    $this->crud->hasAccessOrFail('update');
    // get entry ID from Request (makes sure its the last ID for nested resources)
    $id = $this->crud->getCurrentEntryId() ?? $id;
    $userAttribute = UserAttribute::where('sponsor_id',$id)->first();
    
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

    $hometown              = [
            'name' => 'hometown',
            'type' => 'text',
            'label' => "Tempat Lahir",
            'wrapperAttributes' => [
                        'class' => 'form -grup col-md-6'
                 ]
            ];

    $dateofbirth        =[   // date_picker
        'name'  => 'date_of_birth',
        'type'  => 'date_picker',
        'label' => 'Tanggal Lahir',
        'wrapperAttributes' => [
                 'class' => 'form -grup col-md-6'
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
            'attributes'=>[
                        'required'=>true,
                    ]
        ];
    $churchmemberof        = [
            'name' => 'church_member_of',
            'type' => 'text',
            'label' => "Jemaat Dari Gereja",
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
        'prefix'=>'/storage',
        'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
    ];
    $this->crud->addFields([$username,$email,$firstname,$lastname,
                            $fullname,$hometown,$dateofbirth,$address,
                            $noHP,$churchmemberof,$label,$website,
                            $facebook,$instagram,$linkedin,$myspace,
                            $pinterest,$soundcloud,$tumblr,$twitter,
                            $youtube,$biograpical,$photo

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
    
     UserAttribute::where('sponsor_id', $item->sponsor_id)
    ->update(['website_url' => $request->input('website_url'),
              'facebook_url'=> $request->input('facebook_url'),
              'instagram_url'=> $request->input('instagram_url'),
              'linkedin_url'=> $request->input('linkedin_url'),
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
}