<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use \Venturecraft\Revisionable\RevisionableTrait;

class Users extends Model
{
    use CrudTrait;
    use RevisionableTrait;
    protected  $revisionForceDeleteEnabled = true;
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    protected $table = 'users';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
   // protected $dates = [''];

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

    public function role()
    {
      return $this->belongsTo(UserRole::class, 'user_role_id','user_role_id');
    }
    public function UserAttribute()
    {
      return $this->hasOne(UserAttribute::class,'user_id');
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
    public function setPhotoProfileAttribute($value)
    {

       $attribute_name = "photo_profile";
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

}
