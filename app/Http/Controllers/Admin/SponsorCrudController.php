<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SponsorRequest;
use App\Http\Requests\SponsorUpdateRequest as UpdateRequest;
use App\Models\DataOrder;
use App\Models\OrderDt;
use App\Models\OrderHd;
use App\Models\OrderProject;
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
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {update as traitupdate;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation {show as traitshow;}

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    function setup()
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
    function setupListOperation()
    {

        $this->crud->addColumns([
            [
                'name' => 'full_name',
                'label' => 'Nama',
            ],

            [
                'label' => 'Gereja',
                'name' => 'church_member_of',
            ],
            [
                'label' => 'Kota',
                'name' => 'address',
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
        CRUD::setValidation(SponsorRequest::class);

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
            'upload'=>true,           
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            'prefix' => '/storage/',
        ];

        $this->crud->addFields([$username, $email, $firstname, $lastname,
            $fullname, $passwordvalue, $passwordconfirm,
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

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);
        $this->crud->hasAccessOrFail('update');

        $username = [
            'name' => 'name',
            'type' => 'text',
            'label' => "Nama Pengguna",
            'attributes' => [
                'required' => true,
            ],
        ];
        $email = [
            'name' => 'email',
            'type' => 'text',
            'label' => "Email",
            'attributes' => [
                'required' => true,
            ],
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
            'attributes' => [
                'required' => true,
            ],
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
            'attributes' => [
                'required' => true,
            ],
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
            'type' => 'text',
        ];

        $photo = [
            'label' => "Photo Profile",
            'name' => "photo_profile",
            'type' => 'image',
            'crop' => true, // set to true to allow cropping, false to
            'prefix' => '/storage',
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
        ];
        $this->crud->addFields([$username, $email, $firstname, $lastname,
            $fullname, $passwordvalue, $passwordconfirm, $hometown,
            $dateofbirth, $address, $noHP, $churchmemberof,
            $label, $website, $facebook, $instagram,
            $linkedin, $myspace, $pinterest, $soundcloud,
            $tumblr, $twitter, $youtube, $biograpical, $photo,

        ]);
    }

    function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->addColumns([
            [
                'name' => 'name',
                'label' => 'Nama',
            ],
            [
                'name' => 'first_name',
                'label' => 'Nama Depan',
                'type' => 'text',
            ],

            [
                'name'  => 'last_name',
                'label' => 'Nama Belakang',
                'type'  => 'text'
            ],
            [
                'name'  => 'full_name',
                'label' => 'Nama Lengkap',
                'type'  => 'text'
            ],
            [
                'name'  => 'hometown',
                'label' => 'Tempat Lahir',
                'type'  =>'text',
            ],
            [
                'name'  => 'date_of_birth',
                'label' => 'Tanggal Lahir',
                'type'  => 'text'
            ],
            [
                'name'  => 'address',
                'label' => 'Alamat',
                'type'  => 'text'
            ],
            [
                'name'  => 'no_hp',
                'label' => 'No Hp',
                'type'  => 'text'
            ],
            [
                'name'  => 'church_member_of',
                'label' => 'Member Dari Gereja',
                'type'  =>'text'
            ],
            [
                'name'  => 'email',
                'label' => 'Email',
                'type'  => 'text'
            ],

            [
                'name'  => 'website_url',
                'label' => 'Website',
                'type'  =>  'text'
            ],
            [
                'name'  => 'facebook_url',
                'label' => 'Facebook',
                'type'  => 'text'
            ],
            [
                'name'  => 'instagram_url',
                'label' => 'Instagram',
                'type'  => 'text'
            ],
            [
                'name'  => 'linkedin_url',
                'label' => 'LinkedIn',
                'type'  => 'text'
            ],
            [
                'name'  => 'my_space_url',
                'label' => 'My Space',
                'type'  =>  'text'
            ],
            [
                'name'  => 'pinterest_url',
                'label' => 'Pinterest',
                'type'  =>  'text'
            ],
            [
                'name'  => 'sound_cloud_url',
                'label' => 'Sound Cloud',
                'type'  => 'text'
            ],
            [
                'name'  => 'tumblr_url',
                'label' => 'Tumblr',
                'type'  => 'text'
            ],
            [
                'name'  => 'youtube_url',
                'label' => 'Youtube',
                'type'  => 'text'
            ],
            [
                'name'  => 'biograpical',
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


    function store()
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

    function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $cekChild = DataOrder::where('sponsor_id', $id);
        $child = $cekChild->exists();

        $cekProject = OrderProject::where('sponsor_id',$id);
        $project = $cekProject->exists();

        $id = $this->crud->getCurrentEntryId() ?? $id;
        if ($child == true || $project == true) {
            return response()->json(array('status' => 'error', 'msg' => 'Error!', 'message' => 'The selected data has already had relation with other data.'), 403);
        } else {
            return $this->crud->delete($id);

        }
    }
}
