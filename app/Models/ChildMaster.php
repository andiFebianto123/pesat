<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class ChildMaster extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'child_master';
    protected $primaryKey = 'child_id';
    // public $timestamps = false;
    protected $guarded = ['child_id'];
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
    public function sponsor_type()
    {
      return $this->belongsTo(SponsorType::class, 'sponsor_type_id', 'sponsor_type_id');
    } 
    public function city()
    {
      return $this->belongsTo(City::class, 'hometown','city_id');
    }
    public function religion()
    {
      return $this->belongsTo(Religion::class, 'religion_id','religion_id');
    }
    public function city2()
    {
      return $this->belongsTo(City::class, 'city_id','city_id');
    }
    public function province()
    {
      return $this->belongsTo(Province::class, 'province_id','province_id');
    }
    public function dlp()
    {
      return $this->belongsTo(dlp::class,'child_id','child_id');      
    }
    public function users()
    {
      return $this->hasMany(User::class, 'id','created_by');
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
    public function setImageAttribute($value)
    {
        $attribute_name = "photo_profile";
        $disk = "public";
        $destination_path = "folder_1/subfolder_1";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
       
    }
    public function setFileProfileAttribute($value){
      $attribute_name = "file_profile";
      $disk           = "public";
      $destination_path = "uploads";
      $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

   
    public function DetailDlp($crud)
    {
      $link = "add-dlp";
        return '<a class="btn btn-sm btn-link" href="'.url('admin/dlp/?id='.$this->child_id).'" data-toggle="tooltip" title="Just a demo custom button." id="'.$this->child_id.'" "><i class="fa fa-search"></i> Detail DLP</a>';
    }
  
}
