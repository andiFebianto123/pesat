<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UsersRequest;
use App\Models\User;
use App\Models\UserAttribute;
use App\Models\UserRole;
use App\Models\Users;
use App\Http\Requests\UsersUpdateRequest as UpdateRequest;
use App\Models\ChildMaster;
use App\Models\ProjectMaster;
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
   // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {store as traitstore;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {update as traitupdate;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {edit as traitedit;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation { destroy as traitDestroy; }


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
 
        $username               = [
            'name'  => 'name',
            'type'  => 'text',
            'label' => "username",
            ];
        $email                  = [
                'name'  => 'email',
                'type'  => 'text',
                'label' => "Email",
                ];
        $firstname              = [
                'name'  => 'first_name',
                'type'  => 'text',
                'label' => "First Name",
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                      ]
                ];
        $lastname              = [
                'name'  => 'last_name',
                'type'  => 'text',
                'label' => "Last Name",
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                      ]
                    ];
        $fullname              = [
                'name'    => 'full_name',
                'type'    => 'text',
                'label'   => "Full Name",
                ];
        $role                   = [
            'name'        => 'user_role_id',
            'label'       => "Role",
            'type'        => 'select2_from_array',
            'allows_null' => false,
            'options'     => $this->userrole(),
            ];                   
        $passwordvalue         = [
                'name'   => 'password',
                'type'   => 'password',
                'label'  => "Password",
                ];
        $passwordconfirm    =[
             'name' => 'password_confirmation',
             'type' => 'password',
             'label' => 'Password Confirmation'
         ];

        $hometown              = [
                'name'  => 'hometown',
                'type'  => 'text',
                'label' => "Tempat Lahir",
                'wrapperAttributes' => [
                            'class' => 'form-group col-md-6'
                     ]
                ];

        $dateofbirth           = [   // date_picker
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
        $photo  =[
                    'label' => "Photo Profile",
                    'name'  => "photo_profile",
                    'type'  => 'image',
                    'upload'=> true,
                    'crop'  => true, // set to true to allow cropping, false to disable
                    'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
                    'prefix'=> '/storage/',
        ];

        $this->crud->addFields([$username,$email,$firstname,$lastname,
                                $fullname,$role,$passwordvalue,$passwordconfirm,//$password,
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

    function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->addColumns([
            [
                'name'  => 'first_name',
                'label' => 'First Name',
            ],
            [
                'name'  => 'last_name',
                'label' => 'Last Name',
            ],

            [
                'name'  => 'full_name',
                'label' => 'Full Name',
            ],
            [
                'name'  => 'hometown',
                'label' => 'Tempat Lahir',
            ],
            [
                'name'  => 'date_of_birth',
                'label' => 'Tanggal Lahir',
            ],
            [
                'name'  => 'address',
                'label' => 'Alamat',
            ],
            [
                'name'  => 'no_hp',
                'label' => 'No HP',
            ],
            [
                'name'  => 'church_member_of',
                'label' => 'Member Dari Gereja',
            ],
            [
                'name'  => 'email',
                'label' => 'Email',
            ],

            [
                'name' => 'user_role_id',
                'label' => 'Role',
                'entity' => 'role',
                'attribute' => 'user_role_name', // foreign key attribute that is shown to user
                'model' => 'App\Models\UserRole', // foreign key model
            ],

            [
                'name' => 'website_url',
                'label' => 'Website URL',
            ],
            [
                'name' => 'facebook_url',
                'label' => 'Facebook URL',
            ],
            [
                'name' => 'instagram_url',
                'label' => 'Instagram URL',
            ],
            [
                'name' => 'linkedin_url',
                'label' => 'LinkedIn URL',
            ],
            [
                'name' => 'my_space_url',
                'label' => 'MySpace URL',
            ],
            [
                'name' => 'pinterest_url',
                'label' => 'Pinterest URL',
            ],
            [
                'name' => 'sound_cloud_url',
                'label' => 'Sound Cloud URL',
            ],
            [
                'name' => 'tumblr_url',
                'label' => 'Tumblr URL',
            ],
            [
                'name' => 'twitter_url',
                'label' => 'Twitter URL',
            ],
            [
                'name' => 'youtube_url',
                'label' => 'Youtube URL',
            ],
            [
                'name' => 'biograpical',
                'label' => 'Biograpical',
            ],

            [
                'name' => 'photo_profile',
                'label' => 'Foto Profile',
                'type' => 'image',
                'prefix' => 'storage/',
                'height' => '150px',
                'function' => function ($entry) {
                    return url($entry->photo_profile);
                },
            ],


        ]);

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
        $user = User::where('id',$id)->first();
        $username               = [
                    'name'  => 'name',
                    'type'  => 'text',
                    'label' => "username",
                    ];
                $email                  = [
                        'name'  => 'email',
                        'type'  => 'text',
                        'label' => "Email",
                        ];
                $firstname              = [
                        'name'  => 'first_name',
                        'type'  => 'text',
                        'label' => "First Name",
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-6'
                              ]
                        ];
                $lastname              = [
                        'name'  => 'last_name',
                        'type'  => 'text',
                        'label' => "Last Name",
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-6'
                              ]
                            ];
                $fullname              = [
                        'name'    => 'full_name',
                        'type'    => 'text',
                        'label'   => "Full Name",
                        ];
                $role                   = [
                    'name'        => 'user_role_id',
                    'label'       => "Role",
                    'type'        => 'select2_from_array',
                    'allows_null' => false,
                    'options'     => $this->userrole(),
                    ];                   
                $passwordvalue         = [
                        'name'   => 'password',
                        'type'   => 'password',
                        'label'  => "Password",
                        ];
                $passwordconfirm    =[
                     'name' => 'password_confirmation',
                     'type' => 'password',
                     'label' => 'Password Confirmation'
                 ];
        
                $hometown              = [
                        'name'  => 'hometown',
                        'type'  => 'text',
                        'label' => "Tempat Lahir",
                        'wrapperAttributes' => [
                                    'class' => 'form-group col-md-6'
                             ]
                        ];
        
                $dateofbirth           = [   // date_picker
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
                $photo  =[
                            'label' => "Photo Profile",
                            'name'  => "photo_profile",
                            'type'  => 'image',
                            'upload'=> true,
                            'crop'  => true, // set to true to allow cropping, false to disable
                            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
                            'prefix'=> '/storage/',
                ];
        
                $this->crud->addFields([$username,$email,$firstname,$lastname,
                                        $fullname,$role,$passwordvalue,$passwordconfirm,//$password,
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
      //  $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
       //                     $this->crud->getStrippedSaveRequest());                
   if($request->filled('password') ){
          User::where('id', $request->id)
         ->update(
                  [ 'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'full_name' => $request->input('full_name'),        
                    'user_role_id' => $request->input('user_role_id'),
                    'password' => bcrypt($request->input('password')),
                    'hometown' => $request->input('hometown'),
                    'date_of_birth' => $request->input('date_of_birth'),
                    'address' => $request->input('address'),
                    'no_hp' => $request->input('no_hp'),
                    'church_member_of' => $request->input('church_member_of'),
                    'website_url' => $request->input('website_url'),
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

               }else{
                User::where('id', $request->id)
                ->update(
                         [ 'name' => $request->input('name'),
                           'email' => $request->input('email'),
                           'first_name' => $request->input('first_name'),
                           'last_name' => $request->input('last_name'),
                           'full_name' => $request->input('full_name'),        
                           'user_role_id' => $request->input('user_role_id'),
                           'hometown' => $request->input('hometown'),
                           'date_of_birth' => $request->input('date_of_birth'),
                           'address' => $request->input('address'),
                           'no_hp' => $request->input('no_hp'),
                           'church_member_of' => $request->input('church_member_of'),
                           'website_url' => $request->input('website_url'),
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
               }
        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();
        //$request->has('password') ? bcrypt($request->password) : $this->password;
        // save the redirect choice for next time
       $this->crud->setSaveAction();

        //return $this->crud->performSaveAction($item->getKey());
        return redirect($this->crud->route);
    }
    public function userrole()
    {
        $getsponsor = UserRole::where('deleted_at',null)->get()
                        ->map
                        ->only(['user_role_id', 'user_role_name']);
        $collection=collect($getsponsor);
        $sponsor = $collection->pluck('user_role_name','user_role_id') ? $collection->pluck('user_role_name','user_role_id') : 0/null;
        return $sponsor;
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        

        $cekchild = ChildMaster::where('created_by',$id);
        $cekproject = ProjectMaster::where('created_by',$id);
        $child = $cekchild->exists();
        $project = $cekproject->exists();
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        if($child == true || $project == true){
            return response()->json(array('status'=>'error', 'msg'=>'Error!','message'=>'The selected data has already had relation with other data.'), 403);
        }else{
            return $this->crud->delete($id);

        }
    }
}
