<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Faker\Provider\Image;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Venturecraft\Revisionable\RevisionableTrait;

class ProjectMaster extends Model
{
    use CrudTrait;
    use RevisionableTrait;
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    use SoftDeletes;
    protected $table = 'project_master';
    protected $primaryKey = 'project_id';
    // public $timestamps = false;
    protected $guarded = ['project_id'];
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

    public function users()
    {
      return $this->hasMany(User::class, 'id','created_by');
    }
    public function sponsor_type()
    {
      return $this->belongsTo(SponsorType::class, 'sponsor_type_id', 'sponsor_type_id');
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
    public function setFeaturedImageAttribute($value)
     {
        $attribute_name = "featured_image";
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

    public function DetailImage($crud)
    {
        return '<a class="btn btn-sm btn-link" href="'.url('admin/project-master-detail/'.$this->project_id.'/image').'" data-toggle="tooltip" title="Just a demo custom button." id="'.$this->child_id.'" "><i class="la la-file-photo-o"></i> Detail Image</a>';
    }
}
