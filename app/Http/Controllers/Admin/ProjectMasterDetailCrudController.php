<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProjectMasterDetailRequest;
use App\Models\ProjectMaster;
use App\Models\ProjectMasterDetail;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

/**
 * Class ProjectMasterDetailCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProjectMasterDetailCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ProjectMasterDetail::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project-master-detail');
        CRUD::setEntityNameStrings('project master detail', 'project master details');
        CRUD::operation('list', function() {
            CRUD::removeButton('update');
            CRUD::removeButton('show');
         });
        $this->crud->headerId = \Route::current()->parameter('header_id');

        ProjectMasterDetail::addGlobalScope('header_id', function (Builder $builder) {
            $builder->where('project_id', $this->crud->headerId);
        });

        CRUD::setModel(ProjectMasterDetail::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project-master-detail/' . ($this->crud->headerId ?? '-') . '/image');
        CRUD::setEntityNameStrings('Add Image', 'Add Image');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    public function getProjectMaster($id){
        $cekdata = ProjectMaster::where('project_id', $id);
        $cekdata =  $cekdata->first();
        if($cekdata == null){
            DB::rollback();
            abort(404, trans('Data Tidak Ditemukan'));
        }
        return $cekdata;
    }

    public function CustomListImageDetail($id){
        
        $this->crud->addButtonFromModelFunction('top','button_name','AddImage','end');
        $this->crud->hasAccessOrFail('list');
        $request = $this->crud->validateRequest();
        $this->crud->addClause('where', 'project_id', '=', $id);
        return [
            [
                'name'=>'image_detail',
                'label'=>'Nama File',
                'type' => 'link',
                'wrapper' => [
                    'href' => function ( $crud,$column,$entry,$related_key ) {
                     return  '/pesat/public/storage/'.$entry->image_detail;
                   },
                   
                'target' => '__blank'
               ]
            ],
            
        ]; 
    }
    protected function setupListOperation()
    {
        $this->crud->cekdata = $this->getProjectMaster($this->crud->headerId);

        CRUD::addColumns([
            [
                'name'=>'image_detail',
                'label'=>'Nama File',
                'type' => 'link',
                'wrapper' => [
                    'href' => function ( $crud,$column,$entry,$related_key ) {
                     return  '/pesat/public/storage/'.$entry->image_detail;
                   },
                   
                'target' => '__blank'
               ]
            ]
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
        CRUD::setValidation(ProjectMasterDetailRequest::class);

        $project_id=$this->crud->headerId;
        $projectid         = [
            'name'      => 'project_id',
            'type'      => 'hidden',
            'default'   =>  $project_id,
            'label'     => "id",
          ];
        $imagedetail     = [
            'label'  => "File Gambar",
            'name'   => "image_detail",
            'type'   => 'image',
            'upload' => true,
            'crop'   => true,
//            'prefix' => '/storage/'
          ];
          $discription         = [
            'name'      => 'discription',
            'type'      => 'text',
            'label'     =>  "Keterangan",
          ];
          
          $this->crud->addFields([$projectid,$imagedetail,$discription]);

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
