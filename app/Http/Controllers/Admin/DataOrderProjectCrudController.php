<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Sponsor;
use App\Models\OrderProject;
use App\Traits\RedirectCrud;
use Illuminate\Http\Request;
use App\Models\ProjectMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\DataOrderProjectRequest;
use App\Services\Midtrans\CreateSnapTokenService;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Services\Midtrans\CreateSnapTokenForProjectService;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DataOrderProjectCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DataOrderProjectCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitstore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        edit as traitedit;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitupdate;
    }
    //use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use RedirectCrud;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\DataOrderProject::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/data-order-project');
        CRUD::setEntityNameStrings('data order project', 'data order projects');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addButtonFromModelFunction('line', 'cekstatus', 'cekStatus');
        $this->crud->addButtonFromModelFunction('line', 'cancel', 'cancelOrder');
        $this->crud->addColumns([

            [
                'name' => 'order_project_id',
                'label' => 'Order ID',
                'type' => 'text',

            ],
            [
                'name' => 'sponsor_id',
                'label' => 'Sponsor',
                'entity' => 'sponsorname',
                'attribute' => 'full_name',

            ],
            [
                'name' => 'project_id',
                'label' => 'Nama Proyek',
                'entity' => 'projectname',
                'attribute' => 'title',

            ],
            [
                'name' => 'price',
                'label' => 'Nominal Donasi',
                'prefix' => 'Rp. '
            ],
            [
                'name' => 'payment_status',
                'label' => 'Status Pembayaran',
                'type' => 'radio',
                'options' => [1 => 'Menungggu Pembayaran', 2 => 'Sukses', 3 => 'Batal'],
            ],
            [
                'name' => 'status_midtrans',
                'label' => 'Status Midtrans',
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
        CRUD::setValidation(DataOrderProjectRequest::class);


        $this->crud->addFields([
            [
                'name' => 'sponsor_id',
                'label' => "Nama Sponsor",
                'type' => 'select2_from_array',
                'allows_null' => false,
                'options' => $this->sponsor(),

            ],
            [
                'name' => 'project_id',
                'label' => "Nama Proyek",
                'type' => 'select2_from_array',
                'allows_null' => false,
                'options' => $this->project(),
            ],
            [
                'name'  => 'price',
                'label' => 'Total Donasi'
            ],
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
        //$this->setupCreateOperation();
        CRUD::setValidation(DataOrderProjectRequest::class);
        $sponsor = [
            'name' => 'sponsor_id',
            'label' => "Nama Sponsor",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->sponsor(),

        ];
        $project = [
            'name' => 'project_id',
            'label' => "Nama Proyek",
            'type' => 'select2_from_array',
            'allows_null' => false,
            'options' => $this->project(),
        ];
        $price = [
            'name'  => 'price',
            'type'  => 'text',
            'label' => 'Total Donasi'
        ];
        $this->crud->addFields([

            $sponsor, $project, $price
        ]);
    }

    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        $request = $this->crud->validateRequest();
        DB::beginTransaction();

        try {

            $sponsor = Sponsor::where('sponsor_id', $request->sponsor_id)->first();
            $project = ProjectMaster::where('project_id', $request->project_id)->first();

            $error = [];
            if (empty($sponsor)) {
                $error['sponsor_id'] = ["The selected sponsor is invalid."];
            }
            if (empty($project)) {
                $error['project_id'] = ["The selected project is invalid."];
            }
            if (count($error) != 0) {
                DB::rollback();
                return $this->redirectStoreCrud($error);
            }


            $orderProject = OrderProject::create([
                'sponsor_id' => $request->sponsor_id,
                'project_id' => $request->project_id,
                'price'   => $request->price,
                'payment_status' => 1,
            ]);

            $id = $orderProject->order_project_id;

            $Snaptokenorder = DB::table('order_project')->where('order_project.order_project_id', $id)
                ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_project.sponsor_id')
                ->join('project_master as pm', 'pm.project_id', '=', 'order_project.project_id')
                ->select(
                    'order_project.*',
                    'pm.title',
                    'sm.full_name',
                    'sm.email',
                    'sm.no_hp'
                )
                ->get();

            $midtrans = new CreateSnapTokenForProjectService($Snaptokenorder, $id);
            $snapToken = $midtrans->getSnapToken();
            $orderProject->snap_token = $snapToken;
            $orderProject->order_project_id_midtrans = 'proyek-' . $id;
            $orderProject->save();

            DB::commit();

            // show a success message
            \Alert::success(trans('backpack::crud.insert_success'))->flash();

            // save the redirect choice for next time
            $this->crud->setSaveAction();

            $item = $orderProject;
            $this->data['entry'] = $this->crud->entry = $item;

            return $this->crud->performSaveAction($item->getKey());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function project()
    {

        $getproject = ProjectMaster::get();
        return $getproject->pluck('title', 'project_id');
    }
    public function sponsor()
    {

        $getsponsor = Sponsor::get();
        return $getsponsor->pluck('full_name', 'sponsor_id');
    }

    function validateRepeatableFields($products)
    {
        foreach ($products as $group) {
            Validator::make((array)$group, [
                'price' => 'required|integer|min:1|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/',
            ])->validate();
        }
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');

        $getStatus = OrderProject::where('order_project_id', $id)->firstOrFail();

        $getStatusMidtrans = $getStatus->order_project_id_midtrans;

        try {
            $decoderespon = \Midtrans\Transaction::status($getStatusMidtrans);

            \Alert::error('Tidak dapat melakukan perubahan data karena order proyek telah terdaftar di Midtrans.')->flash();
            return redirect(url($this->crud->route));
        } catch (Exception $e) {
            if ($e->getCode() != 404) {
                \Alert::error('Gagal mendapatkan status order proyek dari Midtrans. [' . $e->getCode() . ']')->flash();
                return redirect(url($this->crud->route));
            }
        }

        if($getStatus->payment_status == 3){
            \Alert::error('Tidak dapat melakukan perubahan data karena order proyek telah dibatalkan.')->flash();
            return redirect(url($this->crud->route));
        }

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());
        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit') . ' ' . $this->crud->entity_name;

        $this->data['id'] = $id;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getEditView(), $this->data);
    }
    public function update($id, Request $request)
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db


        DB::beginTransaction();

        try {
            $sponsor = Sponsor::where('sponsor_id', $request->sponsor_id)->first();
            $project = ProjectMaster::where('project_id', $request->project_id)->first();

            $error = [];
            if (empty($sponsor)) {
                $error['sponsor_id'] = ["The selected sponsor is invalid."];
            }
            if (empty($project)) {
                $error['project_id'] = ["The selected project is invalid."];
            }
            if (count($error) != 0) {
                DB::rollback();
                return $this->redirectUpdateCrud($id, $error);
            }

            $getStatus = OrderProject::where('order_project_id', $id)->firstOrFail();

            $getStatusMidtrans = $getStatus->order_project_id_midtrans;

            try {
                $decoderespon = \Midtrans\Transaction::status($getStatusMidtrans);
                \Alert::error('Tidak dapat melakukan perubahan data karena order proyek telah terdaftar di Midtrans.')->flash();
                DB::rollBack();
                return redirect(url($this->crud->route));
            } catch (Exception $e) {
                if ($e->getCode() != 404) {
                    \Alert::error('Gagal mendapatkan status order proyek dari Midtrans. [' . $e->getCode() . ']')->flash();
                    DB::rollBack();
                    return redirect(url($this->crud->route));
                }
            }

            if($getStatus->payment_status == 3){
                DB::rollBack();
                \Alert::error('Tidak dapat melakukan perubahan data karena order proyek telah dibatalkan.')->flash();
                return redirect(url($this->crud->route));
            }

            $item = $this->crud->update(
                $request->get($this->crud->model->getKeyName()),
                $this->crud->getStrippedSaveRequest()
            );
            $this->data['entry'] = $this->crud->entry = $item;

            $Snaptokenorder = DB::table('order_project')->where('order_project.order_project_id', $item->order_project_id)
                ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_project.sponsor_id')
                ->join('project_master as pm', 'pm.project_id', '=', 'order_project.project_id')
                ->get();

            $code = $item->order_project_id . "-" . Carbon::now()->timestamp;
            $orderIdMidtrans = "proyek-" . $code;
            $midtrans = new CreateSnapTokenForProjectService($Snaptokenorder, $code);
            $snapToken = $midtrans->getSnapToken();
            $this->data['entry']->snap_token = $snapToken;
            $this->data['entry']->price = $item->price;
            $this->data['entry']->order_project_id_midtrans = $orderIdMidtrans;
            $this->data['entry']->save();

            DB::commit();

            // show a success message
            \Alert::success(trans('backpack::crud.update_success'))->flash();

            // save the redirect choice for next time
            $this->crud->setSaveAction();

            return $this->crud->performSaveAction($item->getKey());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
