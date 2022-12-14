<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CustomRevisionableTrait;

class DataOrderProject extends Model
{
  use CrudTrait;
  use CustomRevisionableTrait;
  protected $revisionCreationsEnabled = true;
  protected $revisionForceDeleteEnabled = true;
  /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
  use SoftDeletes;
  protected $table = 'order_project';
  protected $primaryKey = 'order_project_id';
  // public $timestamps = false;
  protected $guarded = ['order_project_id'];
  // protected $fillable = [];
  // protected $hidden = [];
  // protected $dates = [];

  /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

  /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

  public function sponsorname()
  {
    return $this->belongsTo(Sponsor::class, 'sponsor_id', 'sponsor_id');
  }
  public function projectname()
  {
    return $this->belongsTo(ProjectMaster::class, 'project_id', 'project_id');
  }
  /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

  /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

  /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
  public function cekStatus()
  {
    return '<a class="btn btn-sm btn-link" href="' . backpack_url('cek-status/' . $this->order_project_id) . '" id="' . $this->order_project_id . '" "><i class="la la-ticket"></i> Cek Status</a>';
  }
  public function cancelOrder()
  {
    if ($this->payment_status == 3) {
      return;
    }
    return '<a class="btn btn-sm btn-link" href="' . backpack_url('project-cancel-order/' . $this->order_project_id) . '" id="' . $this->order_project_id . '" "><i class="la la-close"></i> Cancel</a>';
  }
}
