<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProjectMasterRequest;
use App\Models\OrderHd;
use App\Models\OrderProject;
use App\Models\ProjectMaster;
use App\Models\ProjectMasterDetail;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

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
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation {show as traitshow;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {store as traitstore;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {update as traitupdate;}
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    function setup()
    {
        CRUD::setModel(\App\Models\ProjectMaster::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project-master');
        CRUD::setEntityNameStrings('Data Proyek', 'Data Proyek');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    function setupListOperation()
    {

        $this->crud->addButtonFromModelFunction('line', 'open_image', 'DetailImage', 'beginning');
        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'Name',
            ],

            [
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
    function setupCreateOperation()
    {
        CRUD::setValidation(ProjectMasterRequest::class);
        $userid = backpack_user()->id;

        $title = [
            'name' => 'title',
            'type' => 'text',
            'label' => "Nama Proyek",
        ];

        $discription = [
            'name' => 'discription',
            'type' => 'ckeditor',
            'label' => "Deskripsi",
            'attributes' => [
                'required' => true,
            ],
        ];
        $label1 = [
            'name' => 'separator',
            'type' => 'custom_html',
            'value' => '',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
                'color' => 'red',
            ],
        ];

        $label2 = [
            'name' => 'lbl2',
            'type' => 'custom_html',
            'value' => '<p style="color:red">*Proyek tanpa tanggal selesai maka tampil akan tampil selamanya</p>',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',

            ],
        ];
        $startdate = [ // date_picker
            'name' => 'start_date',
            'type' => 'date_picker',
            'label' => 'Tanggal Mulai',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'en',
            ],
        ];

        $enddate = [ // date_picker
            'name' => 'end_date',
            'type' => 'date_picker',
            'label' => 'Tanggal Selesai',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'en',
                'clearBtn'  =>true
            ],
        ];
        $amount = [
            'name' => 'amount',
            'label' => 'Nominal',
            'type' => 'text',
        ];
        $lastamount = [
            'name' => 'last_amount',
            'label' => '',
            'type' => 'hidden',
            'default'=>0,
        ];
        $photo = [
            'label' => "Gambar Unggulan",
            'name' => "featured_image",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            'prefix' => '/storage',
        ];
        $createdby = [
            'name' => 'created_by',
            'type' => 'hidden',
            'label' => 'id',
            'default' => $userid,
        ];
        $isclosed = [
            'name' => 'is_closed',
            'type' => 'hidden',
            'default'=>0
        ];
        $this->crud->addFields([$title, $discription,$label1,$label2,$startdate,$enddate,$amount,$lastamount, $photo, $createdby,$isclosed]);

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
        CRUD::setValidation(ProjectMasterRequest::class);

        $title = [
            'name' => 'title',
            'type' => 'text',
            'label' => "Nama Proyek",
        ];

        $discription = [
            'name' => 'discription',
            'type' => 'ckeditor',
            'label' => "Deskripsi",
            'attributes' => [
                'required' => true,
            ],
        ];
        $startdate = [ // date_picker
            'name' => 'start_date',
            'type' => 'date_picker',
            'label' => 'Tanggal Mulai',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'en',
            ],
        ];
        $enddate = [ // date_picker
            'name' => 'end_date',
            'type' => 'date_picker',
            'label' => 'Tanggal Selesai',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-6',
            ],

            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'en',
                'clearBtn'  =>true
            ],
        ];

        $amount = [
            'name' => 'amount',
            'label' => 'Nominal',
            'type' => 'number',
            'prefix'        => 'Rp',
        ];
        $photo = [
            'label' => "Gambar Unggulan",
            'name' => "featured_image",
            'type' => 'image',
            'upload' => true,
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            'prefix' => '/storage',
        ];
        $createdby = [
            'name' => 'created_by',
            'type' => 'hidden',
            'label' => 'id',
        ];

        $isClosed = [
            'name' => 'is_closed',
            'type' => 'hidden',
        ];

        $this->crud->addFields([$title, $discription,$startdate,$enddate,$amount, $photo, $createdby,$isClosed]);
    }

    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // insert item in the db

        $enddate   = $request->end_date;
        $now = Carbon::now();
        $lastamount = $request->last_amount;
        $amount     = $request->amount;
        
     
        if($enddate == null){
            
                $this->crud->getRequest()->request->set('is_closed', 0);
        
        }else{
            
            if($now >= $enddate || $lastamount >= $amount){
        
                $this->crud->getRequest()->request->set('is_closed', 1);                
        
            }else{
        
                $this->crud->getRequest()->request->set('is_closed', 0);
        
            }
        }

        $item = $this->crud->create($this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;


        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        $getProject = ProjectMaster::where('project_id',$request->project_id)
                            ->first();
                            
        $enddate   = $request->end_date;    
        $now = Carbon::now();
        $amount     = $request->amount;
        $lastAmount = $getProject->last_amount;

        if($enddate == null){

            $this->crud->getRequest()->request->set('is_closed', 0);
        
        }else{
        
        if($now >= $enddate || $lastAmount >= $amount){
        
            $this->crud->getRequest()->request->set('is_closed', 1);                
        
        }else{
        
            $this->crud->getRequest()->request->set('is_closed', 0);
        
        }
    }
        // update the row in the db
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
                            $this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }


    function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->addColumns([
            [
                'name' => 'title',
                'label' => 'Judul',
            ],
            [
                'name' => 'discription',
                'label' => 'Diskripsi',
                'type' => 'text',
                'escaped' => false,

            ],
            [
                'name' => 'start_date',
                'label' => 'Tanggal Mulai',
                'type' => 'text',
            ],
            [
                'name' => 'end_date',
                'label' => 'Tanggal Selesai',
                'type' => 'text',
            ],
            [
                'name' => 'amount',
                'label' => 'Nominal yang dibutuhkan',
                'type' => 'text',
                'prefix'=> 'Rp. '
            ],
            [
                'name'     => 'featured_image',
                'label'    => 'Foto',
                'type'     => 'image',
                'prefix'   => 'storage/',
                'height'   => '150px',
                'function' => function($entry) {          
                 return   url($entry->featured_image);
                }
              ],

        ]);
    }
    function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        $cekProject = OrderProject::where('project_id', $id);
        $project = $cekProject->exists();

        $cekImg = ProjectMasterDetail::where('project_id',$id);
        $img    = $cekImg->exists();


        $id = $this->crud->getCurrentEntryId() ?? $id;
        if ($project == true || $img == true) {
            return response()->json(array('status' => 'error', 'msg' => 'Error!', 'message' => 'The selected data has already had relation with other data.'), 403);
        } else {
            return $this->crud->delete($id);

        }
    }
}
