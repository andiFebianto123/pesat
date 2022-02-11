<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CustomRevisionableTrait;

class Dlp extends Model
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

  protected $table = 'dlp';
  protected $primaryKey = 'dlp_id';
  // public $timestamps = false;
  protected $guarded = ['dlp_id'];
  // protected $fillable = [];
  // protected $hidden = [];
  protected $dates = ['deleted_at'];

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
  public function Dlp()
  {
    return $this->hasMany(ChildMaster::class, 'child_id', 'child_id');
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
  public function setFileDlpAttribute($value)
  {
    $attribute_name = "file_dlp";
    $disk           = "public";
    $destination_path = "uploads_dlp";
    $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
  }
  public function AddDlp()
  {

    return '<a href="' . url('admin/dlp/' . $this->child_id . '/detail/create') . '" class="btn btn-primary" data-style="zoom-in">
                  <span class="ladda-label">
                    <i class="la la-plus">
                    </i>
                      Add DLP
                  </span>
                </a>';
  }
  public function Send_Email()
  {
    return '<a class="btn btn-sm btn-link" href="' . backpack_url('send-mail/' . $this->child_id . '/dlp/' . $this->dlp_id) . '" id="' . $this->child_id . '" "><i class="la la-envelope"></i> Kirim Email</a>';
  }
}
