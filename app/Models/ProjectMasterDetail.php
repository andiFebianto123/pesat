<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class ProjectMasterDetail extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'project_master_dt';
    protected $primaryKey = 'project_dt_id';
    // public $timestamps = false;
    protected $guarded = ['project_dt_id'];
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
    public function setImageDetailAttribute($value)
    {
       // $attribute_name = "featured_image";
       // $disk = "public";
       // $destination_path = "image";
       // $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
       $attribute_name = "image_detail";
       $disk = "public";
       $destination_path = "/image";
   
       // if the image was erased
       if ($value==null) {
           // delete the image from disk
           \Storage::disk($disk)->delete($this->{$attribute_name});
   
           // set null in the database column
           $this->attributes[$attribute_name] = null;
       }
   
       // if a base64 was sent, store it in the db
       if (str_starts_with($value, 'data:image'))
       {
           // 0. Make the image
           $image = \Image::make($value)->encode('jpg', 90);
           // 1. Generate a filename.
           $filename = md5($value.time()).'.jpg';
           // 2. Store the image on disk.
           \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());
           // 3. Save the path to the database
           $this->attributes[$attribute_name] = $destination_path.'/'.$filename;
       }
      
    }

    public function AddImage()
    { 
      
       // return '<a class="btn btn-sm btn-link" href="'.url('admin/dlp/create/?id='.$this->child_id).'" data-toggle="tooltip" title="Just a demo custom button." id="'.$this->child_id.'" "><i class="fa fa-search"></i> Add DLP</a>';
      return '<a href="'.url('admin/project-master-detail/create/?projectid='.$this->project_id).'" class="btn btn-primary" data-style="zoom-in">
                <span class="ladda-label">
                  <i class="la la-plus">
                  </i>
                    Add Image Detail
                </span>
              </a>';
//'admin/dlp/create/?childid='.$this->child_id      
    }
}
