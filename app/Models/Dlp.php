<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dlp extends Model
{
    use CrudTrait;

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
      public function Send_Email(){
        return '<a class="btn btn-sm btn-link" href="'.backpack_url('send-mail/'.$this->dlp_id).'" data-toggle="tooltip" title="Just a demo custom button." id="'.$this->child_id.'" "><i class="fa fa-search"></i> Kirim Email</a>';
       
      }
}