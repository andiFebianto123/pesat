<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\City;
use App\Models\Sponsor;
use App\Models\Province;
use App\Models\Religion;
use App\Models\ChildMaster;
use App\Traits\RedirectCrud;
use Illuminate\Http\Request;
use App\Models\DataDetailOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ChildMasterRequest;
use Illuminate\Support\Facades\Validator;
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
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation {
        show as traitshow;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {
        destroy as traitDestroy;
    }
    use RedirectCrud;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    function setup()
    {
        CRUD::setModel(\App\Models\ChildMaster::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/child-master');
        CRUD::setEntityNameStrings('Data', 'Data Anak');
        $this->crud->dateNow = Carbon::now()->startOfDay();
        $this->crud->setShowView(backpack_view('child_master_show'));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    function setupListOperation()
    {
        $this->crud->enableBulkActions();
        $this->crud->addButtonFromModelFunction('line', 'open_dlp', 'DetailDlp', 'beginning');
        $this->crud->addButtonFromView('top', 'update_sponsor', 'update_sponsor', 'ending');
        $this->crud->addButtonFromView('top', 'update_not_sponsor', 'update_not_sponsor', 'ending');
        $this->crud->addFilter(
            [
                'name'  => 'is_sponsored',
                'type'  => 'dropdown',
                'label' => 'Status'
            ],
            [
                1 => 'Tersponsori',
                0 => 'Belum Tersponsori',
            ],
            function ($value) { // if the filter is active
                $this->crud->addClause('where', function ($query) use ($value) {
                    if ($value === "1") {
                        $query->where('is_sponsored', 1)
                            ->orWhereHas('detailorders', function ($innerQuery) {
                                $innerQuery->whereDate('start_order_date', '<=', $this->crud->dateNow)
                                    ->whereDate('end_order_date', '>=', $this->crud->dateNow)
                                    ->whereHas('order', function ($deepestQuery) {
                                        $deepestQuery->where('payment_status', '<=', 2);
                                    });
                            });
                    } else if ($value === "0") {
                        $query->where('is_sponsored', 0)->whereDoesntHave('detailorders', function ($innerQuery) {
                            $innerQuery->whereDate('start_order_date', '<=', $this->crud->dateNow)
                                ->whereDate('end_order_date', '>=', $this->crud->dateNow)
                                ->whereHas('order', function ($deepestQuery) {
                                    $deepestQuery->where('payment_status', '<=', 2);
                                });
                        });
                    }
                });
            }
        );
        //     $this->crud->removeButton('delete');
        $this->crud->addColumns([
            [
                'name' => 'registration_number',
                'label' => 'No Induk'
            ],
            [
                'name' => 'full_name',
                'label' => 'Name',
            ],

            [
                'type' => 'select',
                'name' => 'created_by', // the relationship name in your Model
                'label' => 'Last Author',
                'entity' => 'users', // the relationship name in your Model
                'attribute' => 'name', // attribute on Article that is shown to admin
                'model'     => "App\Models\Users",
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query->leftJoin('users', 'users.id', '=', 'child_master.created_by')
                        ->orderBy('users.name', $columnDirection)->select('child_master.*');
                }
            ],
            [
                'type' => 'closure',
                'orderable' => false,
                'label' => 'Status',
                'function' => function ($entry) {
                    if ($entry->is_sponsored) {
                        return 'Tersponsori (Offline)';
                    } else if (ChildMaster::getStatusSponsor($entry->child_id, $this->crud->dateNow)) {
                        return 'Tersponsori';
                    }
                    return 'Belum Tersponsori';
                }
            ],
            // [
            //     'name' => 'DLP',
            //     'type' => 'boolean',
            //     'options' => [0 => 'Not Sent', 1 => 'Sent'],
            // ],
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
        CRUD::setValidation(ChildMasterRequest::class);

        $createdby = [
            'name' => 'created_by',
            'type' => 'hidden',
        ];
        $name = [
            'name' => 'full_name',
            'type' => 'text',
            'label' => "Nama Lengkap",
            'attributes' => [],
        ];

        $childdiscription = [
            'name' => 'child_discription',
            'label' => 'Deskripsi Anak',
            'type' => 'ckeditor',
        ];
        $noRegistration = [
            'name' => 'registration_number',
            'label' => 'No Induk',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $nickname = [
            'name' => 'nickname',
            'label' => 'Nama Panggilan',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $gender = [
            'name' => 'gender',
            'label' => 'Jenis Kelamin',
            'type' => 'select2_from_array',
            'allows_null' => false,
            'default'     => 'one',
            'options' => ['L' => 'L', 'P' => 'P'],
        ];

        $hometown = [
            'name' => 'hometown',
            'label' => "Tempat Lahir",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->hometown(),
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

        $religion = [
            'name' => 'religion_id',
            'label' => "Agama",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->religion(),

        ];
        $FC = [
            'name' => 'fc',
            'label' => 'FC',
            'type' => 'text',
        ];
        $price = [
            'name' => 'price',
            'label' => 'Nominal',
            'type' => 'text',
        ];
        $sponsor = [
            'name' => 'sponsor_name',
            'label' => 'Sponsor',
            'type' => 'text',
        ];

        $districts = [
            'name' => 'districts',
            'label' => 'Kecamatan',
            'type' => 'text',
        ];
        $province = [ // 1-n relationship
            'label' => "Propinsi", // Table column heading
            'type' => "select2_from_ajax",
            'name' => 'province_id', // the column that contains the ID of that connected entity
            'entity' => 'province', // the method that defines the relationship in your Model
            'attribute' => "province_name", // foreign key attribute that is shown to user
            'data_source' => url("admin/api/province-select"), // url to controller search function (with /{id} should return model)
            'placeholder' => "Select a Province", // placeholder for the select
            'minimum_input_length' => 2, // minimum characters to type before querying results
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $city = [ // 1-n relationship
            'label' => "Kabupaten", // Table column heading
            'type' => "select2_from_ajax",
            'name' => 'city_id', // the column that contains the ID of that connected entity
            'entity' => 'city2', // the method that defines the relationship in your Model
            'attribute' => "city_name", // foreign key attribute that is shown to user
            'data_source' => url("admin/api/city-select"), // url to controller search function (with /{id} should return model)
            'placeholder' => "Select a City", // placeholder for the select
            'minimum_input_length' => 2, // minimum characters to type before querying results
            'include_all_form_fields' => true,
            'dependencies' => ['province_id'],
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];

        $father = [
            'name' => 'father',
            'label' => 'Ayah',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $mother = [
            'name' => 'mother',
            'label' => 'Ibu',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $profession = [
            'name' => 'profession',
            'label' => 'Pekerjaan',
            'type' => 'text',
        ];
        $economy = [
            'name' => 'economy',
            'label' => 'Ekonomi',
            'type' => 'text',
        ];
        $class = [
            'name' => 'class',
            'label' => 'Kelas',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
        ];
        $school = [
            'name' => 'school',
            'label' => 'Sekolah',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
        ];
        $schoolyear = [
            'name' => 'school_year',
            'label' => 'Tahun Ajaran',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
        ];

        $signinfc = [ // date_picker
            'name' => 'sign_in_fc',
            'type' => 'date_picker',
            'label' => 'Masuk FC',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'en',
            ],
        ];

        $leavefc = [ // date_picker
            'name' => 'leave_fc',
            'type' => 'date_picker',
            'label' => 'Keluar FC',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'en',
            ],
        ];
        $reasontoleave = [
            'name' => 'reason_to_leave',
            'label' => 'Alasan Keluar',
            'type' => 'textarea',
        ];

        $internaldiscription = [
            'name' => 'internal_discription',
            'label' => 'Keterangan Internal',
            'type' => 'textarea',
        ];

        $photo = [
            'label' => "Profile Image",
            'name' => "photo_profile",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            'prefix' => '/storage/',
            'hint' => 'Rekomendasi ukuran gambar 600 x 600 px'
        ];

        $fileprofile = [
            'label' => "File Profile",
            'name' => "file_profile",
            'type' => 'upload',
            'upload' => true,
            'crop' => false,
            'disks' => 'uploads',
            'prefix' => '/storage/',
        ];

        $this->crud->addFields([
            $createdby, $name, $childdiscription, $noRegistration,
            $nickname, $gender, $hometown, $dateofbirth,
            $religion, $FC, $price, $sponsor, $province,
            $city, $districts, $father, $mother,
            $profession, $economy, $class, $school,
            $schoolyear, $signinfc, $leavefc, $reasontoleave,
            $internaldiscription, $photo, $fileprofile,
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
        //  CRUD::setValidation(UpdateRequest::class);
        CRUD::setValidation(ChildMasterRequest::class);
        $createdby = [
            'name' => 'created_by',
            'type' => 'hidden',
        ];
        $name = [
            'name' => 'full_name',
            'type' => 'text',
            'label' => "Nama Lengkap",
            'attributes' => [],
        ];

        $childdiscription = [
            'name' => 'child_discription',
            'label' => 'Deskripsi Anak',
            'type' => 'ckeditor',
        ];
        $noRegistration = [
            'name' => 'registration_number',
            'label' => 'No Induk',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $nickname = [
            'name' => 'nickname',
            'label' => 'Nama Panggilan',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $gender = [
            'name' => 'gender',
            'label' => 'Jenis Kelamin',
            'type' => 'select2_from_array',
            'allows_null' => false,
            'default'     => 'one',
            'options' => ['L' => 'L', 'P' => 'P'],
        ];

        $hometown = [
            'name' => 'hometown',
            'label' => "Tempat Lahir",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->hometown(),
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

        $religion = [
            'name' => 'religion_id',
            'label' => "Agama",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->religion(),

        ];
        $FC = [
            'name' => 'fc',
            'label' => 'FC',
            'type' => 'text',
        ];
        $price = [
            'name' => 'price',
            'label' => 'Nominal',
            'type' => 'text',
        ];
        $sponsor = [
            'name' => 'sponsor_name',
            'label' => 'Sponsor',
            'type' => 'text',
        ];

        $districts = [
            'name' => 'districts',
            'label' => 'Kecamatan',
            'type' => 'text',
        ];
        $province = [ // 1-n relationship
            'label' => "Propinsi", // Table column heading
            'type' => "select2_from_ajax",
            'name' => 'province_id', // the column that contains the ID of that connected entity
            'entity' => 'province', // the method that defines the relationship in your Model
            'attribute' => "province_name", // foreign key attribute that is shown to user
            'data_source' => url("admin/api/province-select"), // url to controller search function (with /{id} should return model)
            'placeholder' => "Select a Province", // placeholder for the select
            'minimum_input_length' => 2, // minimum characters to type before querying results
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $city = [ // 1-n relationship
            'label' => "Kabupaten", // Table column heading
            'type' => "select2_from_ajax",
            'name' => 'city_id', // the column that contains the ID of that connected entity
            'entity' => 'city2', // the method that defines the relationship in your Model
            'attribute' => "city_name", // foreign key attribute that is shown to user
            'data_source' => url("admin/api/city-select"), // url to controller search function (with /{id} should return model)
            'placeholder' => "Select a City", // placeholder for the select
            'minimum_input_length' => 2, // minimum characters to type before querying results
            'include_all_form_fields' => true,
            'dependencies' => ['province_id'],
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];

        $father = [
            'name' => 'father',
            'label' => 'Ayah',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $mother = [
            'name' => 'mother',
            'label' => 'Ibu',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],
        ];
        $profession = [
            'name' => 'profession',
            'label' => 'Pekerjaan',
            'type' => 'text',
        ];
        $economy = [
            'name' => 'economy',
            'label' => 'Ekonomi',
            'type' => 'text',
        ];
        $class = [
            'name' => 'class',
            'label' => 'Kelas',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
        ];
        $school = [
            'name' => 'school',
            'label' => 'Sekolah',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
        ];
        $schoolyear = [
            'name' => 'school_year',
            'label' => 'Tahun Ajaran',
            'type' => 'text',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4',
            ],
        ];

        $signinfc = [ // date_picker
            'name' => 'sign_in_fc',
            'type' => 'date_picker',
            'label' => 'Masuk FC',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'en',
            ],
        ];

        $leavefc = [ // date_picker
            'name' => 'leave_fc',
            'type' => 'date_picker',
            'label' => 'Keluar FC',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'en',
            ],
        ];
        $reasontoleave = [
            'name' => 'reason_to_leave',
            'label' => 'Alasan Keluar',
            'type' => 'textarea',
        ];

        $internaldiscription = [
            'name' => 'internal_discription',
            'label' => 'Keterangan Internal',
            'type' => 'textarea',
        ];

        $photo = [
            'label' => "Profile Image",
            'name' => "photo_profile",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            'prefix' => '/storage/',
            'hint' => 'Rekomendasi ukuran gambar 600 x 600 px'
        ];

        $fileprofile = [
            'label' => "File Profile",
            'name' => "file_profile",
            'type' => 'upload',
            'upload' => true,
            'crop' => false,
            'disks' => 'uploads',
            'prefix' => '/storage/',
        ];

        $this->crud->addFields([
            $createdby, $name, $childdiscription, $noRegistration,
            $nickname, $gender, $hometown, $dateofbirth,
            $religion, $FC, $price, $sponsor, $province,
            $city, $districts, $father, $mother,
            $profession, $economy, $class, $school,
            $schoolyear, $signinfc, $leavefc, $reasontoleave,
            $internaldiscription, $photo, $fileprofile,
        ]);
    }

    function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->addColumns([
            [
                'name' => 'full_name',
                'label' => 'Nama Lengkap',
            ],
            [
                'type' => 'closure',
                'orderable' => false,
                'label' => 'Status',
                'function' => function ($entry) {
                    if ($entry->is_sponsored) {
                        return 'Tersponsori (Offline)';
                    } else if (ChildMaster::getStatusSponsor($entry->child_id, $this->crud->dateNow)) {
                        return 'Tersponsori';
                    }
                    return 'Belum Tersponsori';
                }
            ],
            [
                'name' => 'child_discription',
                'label' => 'Diskripsi Anak',
                'type' => 'text',
                'escaped' => false,

            ],

            [
                'name' => 'registration_number',
                'label' => 'No Induk',
            ],
            [
                'name' => 'nickname',
                'label' => 'Nama Panggilan',
            ],
            [
                'name' => 'gender',
                'label' => 'Jenis Kelamin',
            ],
            [
                'name' => 'hometown',
                'label' => 'Tempat Lahir',
                'entity' => 'city',
                'attribute' => 'city_name', // foreign key attribute that is shown to user
                'model' => 'App\Models\City', // foreign key model
            ],
            [
                'name' => 'date_of_birth',
                'label' => 'Tanggal Lahir',
            ],
            [
                'name' => 'religion_id',
                'label' => 'Agama',
                'entity' => 'religion',
                'attribute' => 'religion_name',
                'model' => 'App\Models\Religion',
            ],
            [
                'name' => 'fc',
                'label' => 'FC',
            ],
            [
                'name' => 'price',
                'label' => 'Nominal',
                'type' => 'number',
                'prefix' => 'Rp. ',
                'decimals'      => 2,
                'dec_point'     => ',',
                'thousands_sep' => '.',
            ],
            [
                'name' => 'price',
                'label' => 'Nominal',
            ],
            [
                'name' => 'sponsor_name',
                'label' => 'Nama Sponsor',
            ],
            [
                'name' => 'districts',
                'label' => 'Kecamatan',
            ],
            [
                'name' => 'province_id',
                'label' => 'Provinsi',
                'entity' => 'province',
                'attribute' => 'province_name',
                'model' => 'App\Models\Province',
            ],
            [
                'name' => 'city_id',
                'label' => 'Kota/Kabupaten',
                'entity' => 'city2',
                'attribute' => 'city_name',
                'model' => 'App\Models\City',
            ],
            [
                'name' => 'father',
                'label' => 'Ayah',
            ],
            [
                'name' => 'mother',
                'label' => 'Ibu',
            ],
            [
                'name' => 'profession',
                'label' => 'Pekerjaan',
            ],
            [
                'name' => 'economy',
                'label' => 'Ekonomi',
            ],
            [
                'name' => 'class',
                'label' => 'Kelas',
            ],
            [
                'name' => 'school',
                'label' => 'Sekolah',
            ],
            [
                'name' => 'school_year',
                'label' => 'Tahun Ajaran',
            ],
            [
                'name' => 'sign_in_fc',
                'label' => 'Masuk FC',
            ],
            [
                'name' => 'leave_fc',
                'label' => 'Keluar FC',
            ],
            [
                'name' => 'reason_to_leave',
                'label' => 'Alasan Keluar',
            ],
            [
                'name' => 'internal_discription',
                'label' => 'Diskripsi Internal',
            ],

            [
                'type' => 'select',
                'name' => 'created_by', // the relationship name in your Model
                'label' => 'Last Author',
                'entity' => 'users', // the relationship name in your Model
                'attribute' => 'name', // attribute on Article that is shown to admin
                'model'     => "App\Models\Users",
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

            [
                'name' => 'file',
                'label' => 'File Profile',
                'type' => 'custom_html',
                'value' => '<span>File</span>',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $related_key) {
                        return url('storage/' . $entry->file_profile);
                    },

                    'target' => '__blank',
                ],

            ],

        ]);
    }

    function hometown()
    {
        $getcity = City::get();
        return $getcity->pluck('city_name', 'city_id');
    }

    function religion()
    {
        $getreligion = Religion::get();
        return $getreligion->pluck('religion_name', 'religion_id');
    }

    function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        $error = [];

        $cekprovince = Province::where('province_id', $request->province_id);
        $province = $cekprovince->exists();

        $cekcity = City::where('province_id', $request->province_id)
            ->where('city_id', $request->city_id);
        $city = $cekcity->exists();

        $cekhometown = City::where('city_id', $request->hometown);
        $hometown = $cekhometown->exists();
        $cekreligion = Religion::where('religion_id', $request->religion_id);
        $religion = $cekreligion->exists();

        if (!$province) {
            $error['province_id'] = ['The selected Province is not valid'];
        }
        if (!$city) {
            $error['city_id'] = ['The selected City is not valid on province'];
        }
        if (!$hometown) {
            $error['hometown'] = ['The selected Hometown is not valid'];
        }
        if (!$religion) {
            $error['religion_id'] = ['The selected Religion is not valid'];
        }

        if (count($error) > 0) {
            return $this->redirectStoreCrud($error);
        }

        // MERGE USER
        $this->crud->getRequest()->merge(['created_by' => backpack_user()->id]);

        $this->crud->getRequest()->merge(['class' => $this->crud->getRequest()->class ?? '' , 'school' => $this->crud->getRequest()->school ?? '']);

        // insert item in the db
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
        $error = [];

        $id = $request->id;

        $cekprovince = Province::where('province_id', $request->province_id);
        $province = $cekprovince->exists();

        $cekcity = City::where('province_id', $request->province_id)
            ->where('city_id', $request->city_id);
        $city = $cekcity->exists();

        $cekhometown = City::where('city_id', $request->hometown);
        $hometown = $cekhometown->exists();
        $cekreligion = Religion::where('religion_id', $request->religion_id); //
        $religion = $cekreligion->exists();

        if (!$province) {
            $error['province_id'] = ['The selected Province is not valid'];
        }
        if (!$city) {
            $error['city_id'] = ['The selected City is not valid on province'];
        }
        if (!$hometown) {
            $error['hometown'] = ['The selected Hometown is not valid'];
        }
        if (!$religion) {
            $error['religion_id'] = ['The selected Religion is not valid'];
        }

        if (count($error) > 0) {
            return $this->redirectUpdateCrud($id, $error);
        }

        // MERGE USER
        $this->crud->getRequest()->merge(['created_by' => backpack_user()->id]);

        $this->crud->getRequest()->merge(['class' => $this->crud->getRequest()->class ?? '' , 'school' => $this->crud->getRequest()->school ?? '']);

        $item = $this->crud->update(
            $request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest()
        );
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $checkchild = DataDetailOrder::where('child_id', $id);
        $child = $checkchild->exists();
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        if ($child == true) {
            return response()->json(array('status' => 'error', 'msg' => 'Error!', 'message' => 'The selected data has already had relation with other data.'), 403);
        } else {
            return $this->crud->delete($id);
        }
    }

    function addSponsor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entries' => 'required|array|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Mohon memilih minimal 1 anak yang ingin diubah.'], 422);
        }
        $entriChilds = request()->input('entries');
        DB::beginTransaction();
        try {
            $errors = [];
            $now = Carbon::now()->startOfDay();
            foreach ($entriChilds as $id) {
                $child = ChildMaster::find($id);
                if ($child !== null) {
                    $isOnlineSponsored = ChildMaster::getStatusSponsor($id, $now);
                    if ($isOnlineSponsored) {
                        $errors[] = 'Anak ' . $child->full_name . ' telah disponsori secara online.';
                    } else if (count($errors) == 0) {
                        $child->is_sponsored = 1;
                        $child->save();
                    }
                }
            }
            if (count($errors) != 0) {
                DB::rollback();
                return response()->json([
                    'message' => collect($errors)->join('</br>')
                ], 422);
            }
            DB::commit();
            return response()->json([
                'message' => 'Berhasil ubah status sponsor'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    function removeSponsor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entries' => 'required|array|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Mohon memilih minimal 1 anak yang ingin diubah.'], 422);
        }
        $entriChilds = request()->input('entries');
        DB::beginTransaction();
        try {
            foreach ($entriChilds as $id) {
                $child = ChildMaster::find($id);
                if ($child !== null) {
                    $child->is_sponsored = 0;
                    $child->save();
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Berhasil ubah status sponsor'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
