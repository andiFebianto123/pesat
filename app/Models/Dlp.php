<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Dlp extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'dlp';
    protected $primaryKey = 'dlp_id';
    // public $timestamps = false;
    protected $guarded = ['dlp_id'];
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
    public function Dlp()
    {
      return $this->hasMany(ChildMaster::class,'child_id','child_id');  
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
    public function setFileDlpAttribute($value){
        $attribute_name = "file_dlp";
        $disk           = "public";
        $destination_path = "uploads_dlp";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
      }
    public function AddDlp()
      { 
        
        return '<a href="'.url('admin/dlp/'.$this->child_id.'/detail/create').'" class="btn btn-primary" data-style="zoom-in">
                  <span class="ladda-label">
                    <i class="la la-plus">
                    </i>
                      Add DLP
                  </span>
                </a>';
      }
}
//AddDlp
//url('admin/dlp/create/?childid='.$this->child_id)