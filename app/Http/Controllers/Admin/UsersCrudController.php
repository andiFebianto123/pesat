<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UsersRequest;
use App\Http\Requests\UsersUpdateRequest as UpdateRequest;
use App\Models\ChildMaster;
use App\Models\ProjectMaster;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Users;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {update as traitupdate;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {destroy as traitDestroy;}

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    function setup()
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
    function setupListOperation()
    {
        //CRUD::setFromDb();
        $this->crud->addColumns([
            [
                'name' => 'name',
                'label' => 'Username',
            ],
            [
                'name' => 'email',
                'label' => 'Email',
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
    function setupCreateOperation()
    {
        CRUD::setValidation(UsersRequest::class);

        $username = [
            'name' => 'name',
            'type' => 'text',
            'label' => "Nama Pengguna",
        ];
        $email = [
            'name' => 'email',
            'type' => 'text',
            'label' => "Email",
        ];
        $firstname = [
            'name' => 'first_name',
            'type' => 'text',
            'label' => "Nama Depan",
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $lastname = [
            'name' => 'last_name',
            'type' => 'text',
            'label' => "Nama Belakang",
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $fullname = [
            'name' => 'full_name',
            'type' => 'text',
            'label' => "Nama Lengkap",
        ];
        $role = [
            'name' => 'user_role_id',
            'label' => "Role",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->userrole(),
        ];
        $passwordvalue = [
            'name' => 'password',
            'type' => 'password',
            'label' => "Password",
        ];
        $passwordconfirm = [
            'name' => 'password_confirmation',
            'type' => 'password',
            'label' => 'Konfirmasi Password',
        ];

        $hometown = [
            'name' => 'hometown',
            'type' => 'text',
            'label' => "Tempat Lahir",
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];

        $dateofbirth = [ // date_picker
            'name' => 'date_of_birth',
            'type' => 'date_picker',
            'label' => 'Tanggal Lahir',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'en',
            ],
        ];
        $address = [
            'name' => 'address',
            'type' => 'text',
            'label' => "Tempat Tinggal",
        ];
        $noHP = [
            'name' => 'no_hp',
            'type' => 'text',
            'label' => "No Hp",
        ];
        $churchmemberof = [
            'name' => 'church_member_of',
            'type' => 'text',
            'label' => "Jemaat Dari Gereja",

        ];
        $label = [
            'name' => 'separator',
            'type' => 'custom_html',
            'value' => '<h4>Contact Info</h4>',
        ];

        $website = [
            'label' => "Website",
            'name' => "website_url",
            'type' => 'text',
        ];
        $facebook = [
            'label' => "Facebook",
            'name' => "facebook_url",
            'type' => 'text',
        ];
        $instagram = [
            'label' => "Instagram",
            'name' => "instagram_url",
            'type' => 'text',
        ];
        $linkedin = [
            'label' => "Linkedin",
            'name' => "linkedin_url",
            'type' => 'text',
        ];
        $myspace = [
            'label' => "MySpace",
            'name' => "my_space_url",
            'type' => 'text',
        ];
        $pinterest = [
            'label' => "Pinterest",
            'name' => "pinterest_url",
            'type' => 'text',
        ];
        $soundcloud = [
            'label' => "SoundCloud",
            'name' => "sound_cloud_url",
            'type' => 'text',
        ];
        $tumblr = [
            'label' => "Tumblr",
            'name' => "tumblr_url",
            'type' => 'text',
        ];
        $twitter = [
            'label' => "Twitter",
            'name' => "twitter_url",
            'type' => 'text',
        ];
        $youtube = [
            'label' => "Youtube",
            'name' => "youtube_url",
            'type' => 'text',
        ];
        $biograpical = [
            'label' => "Biograpical",
            'name' => "biograpical",
            'type' => 'textarea',
        ];
        $photo = [
            'label' => "Photo Profile",
            'name' => "photo_profile",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            'prefix' => '/storage/',
        ];

        $this->crud->addFields([$username, $email, $firstname, $lastname,
            $fullname, $role, $passwordvalue, $passwordconfirm, //$password,
            $hometown, $dateofbirth, $address, $noHP,
            $churchmemberof, $label, $website, $facebook,
            $instagram, $linkedin, $myspace, $pinterest,
            $soundcloud, $tumblr, $twitter, $youtube,
            $biograpical, $photo,

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
                'name' => 'first_name',
                'label' => 'First Name',
            ],
            [
                'name' => 'last_name',
                'label' => 'Last Name',
            ],

            [
                'name' => 'full_name',
                'label' => 'Full Name',
            ],
            [
                'name' => 'hometown',
                'label' => 'Tempat Lahir',
            ],
            [
                'name' => 'date_of_birth',
                'label' => 'Tanggal Lahir',
            ],
            [
                'name' => 'address',
                'label' => 'Alamat',
            ],
            [
                'name' => 'no_hp',
                'label' => 'No HP',
            ],
            [
                'name' => 'church_member_of',
                'label' => 'Member Dari Gereja',
            ],
            [
                'name' => 'email',
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
            
    function setupUpdateOperation()
    {
        //$this->setupCreateOperation();
        CRUD::setValidation(UpdateRequest::class);
        $this->crud->hasAccessOrFail('update');

        $username = [
            'name' => 'name',
            'type' => 'text',
            'label' => "Nama Pengguna",
        ];
        $email = [
            'name' => 'email',
            'type' => 'text',
            'label' => "Email",
        ];
        $firstname = [
            'name' => 'first_name',
            'type' => 'text',
            'label' => "Nama Depan",
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $lastname = [
            'name' => 'last_name',
            'type' => 'text',
            'label' => "Nama Belakang",
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $fullname = [
            'name' => 'full_name',
            'type' => 'text',
            'label' => "Nama Lengkap",
        ];
        $role = [
            'name' => 'user_role_id',
            'label' => "Role",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->userrole(),
        ];
        $passwordvalue = [
            'name' => 'password',
            'type' => 'password',
            'label' => "Password",
        ];
        $passwordconfirm = [
            'name' => 'password_confirmation',
            'type' => 'password',
            'label' => 'Konfirmas Password',
        ];

        $hometown = [
            'name' => 'hometown',
            'type' => 'text',
            'label' => "Tempat Lahir",
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];

        $dateofbirth = [ // date_picker
            'name' => 'date_of_birth',
            'type' => 'date_picker',
            'label' => 'Tanggal Lahir',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'en',
            ],
        ];
        $address = [
            'name' => 'address',
            'type' => 'text',
            'label' => "Tempat Tinggal",
        ];
        $noHP = [
            'name' => 'no_hp',
            'type' => 'text',
            'label' => "No Hp",
        ];
        $churchmemberof = [
            'name' => 'church_member_of',
            'type' => 'text',
            'label' => "Jemaat Dari Gereja",

        ];
        $label = [
            'name' => 'separator',
            'type' => 'custom_html',
            'value' => '<h4>Contact Info</h4>',
        ];

        $website = [
            'label' => "Website",
            'name' => "website_url",
            'type' => 'text',
        ];
        $facebook = [
            'label' => "Facebook",
            'name' => "facebook_url",
            'type' => 'text',
        ];
        $instagram = [
            'label' => "Instagram",
            'name' => "instagram_url",
            'type' => 'text',
        ];
        $linkedin = [
            'label' => "Linkedin",
            'name' => "linkedin_url",
            'type' => 'text',
        ];
        $myspace = [
            'label' => "MySpace",
            'name' => "my_space_url",
            'type' => 'text',
        ];
        $pinterest = [
            'label' => "Pinterest",
            'name' => "pinterest_url",
            'type' => 'text',
        ];
        $soundcloud = [
            'label' => "SoundCloud",
            'name' => "sound_cloud_url",
            'type' => 'text',
        ];
        $tumblr = [
            'label' => "Tumblr",
            'name' => "tumblr_url",
            'type' => 'text',
        ];
        $twitter = [
            'label' => "Twitter",
            'name' => "twitter_url",
            'type' => 'text',
        ];
        $youtube = [
            'label' => "Youtube",
            'name' => "youtube_url",
            'type' => 'text',
        ];
        $biograpical = [
            'label' => "Biograpical",
            'name' => "biograpical",
            'type' => 'textarea',
        ];
        $photo = [
            'label' => "Photo Profile",
            'name' => "photo_profile",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            'prefix' => '/storage/',
        ];

        $this->crud->addFields([$username, $email, $firstname, $lastname,
            $fullname, $role, $passwordvalue, $passwordconfirm, //$password,
            $hometown, $dateofbirth, $address, $noHP,
            $churchmemberof, $label, $website, $facebook,
            $instagram, $linkedin, $myspace, $pinterest,
            $soundcloud, $tumblr, $twitter, $youtube,
            $biograpical, $photo,

        ]);
    }
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // insert item in the db
        $hashpass = bcrypt($request->input('password'));
        $this->crud->getRequest()->request->set('password', $hashpass);

        $item = $this->crud->create($this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
    function update()
    {
        $this->crud->hasAccessOrFail('update');
        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db
        if ($request->filled('password')) {

            $hashpass = bcrypt($request->input('password'));

            $this->crud->getRequest()->request->set('password', $hashpass);

        } else {

            $this->crud->getRequest()->request->remove('password');

        }

        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();
        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
        //   return redirect($this->crud->route);
    }

    function userrole()
    {
        $getsponsor = UserRole::get()
            ->map
            ->only(['user_role_id', 'user_role_name']);
        $collection = collect($getsponsor);
        $sponsor = $collection->pluck('user_role_name', 'user_role_id') ? $collection->pluck('user_role_name', 'user_role_id') : 0 / null;
        return $sponsor;
    }

    function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $cekchild = ChildMaster::where('created_by', $id);
        $cekproject = ProjectMaster::where('created_by', $id);
        $child = $cekchild->exists();
        $project = $cekproject->exists();
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        if ($child == true || $project == true) {
            return response()->json(array('status' => 'error', 'msg' => 'Error!', 'message' => 'The selected data has already had relation with other data.'), 403);
        } else {
            return $this->crud->delete($id);

        }
    }
}
