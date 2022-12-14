<?php

namespace App\Models;

use App\Traits\CustomRevisionableTrait;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Sponsor extends Model
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

    protected $table = 'sponsor_master';
    protected $primaryKey = 'sponsor_id';
    // public $timestamps = false;
    protected $guarded = ['sponsor_id'];
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
    public function UserAttribute()
    {
        return $this->hasOne(UserAttribute::class, 'sponsor_id');
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
    public function data_order()
    {
        return $this->hasMany(DataOrder::class, 'sponsor_id');
    }

    public function project_order()
    {
        return $this->hasMany(DataOrderProject::class, 'sponsor_id');
    }
    public function setPhotoProfileAttribute($value)
    {
        // $attribute_name = "featured_image";
        // $disk = "public";
        // $destination_path = "image";
        // $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
        $attribute_name = "photo_profile";
        $disk = "public";
        $destination_path = "/image";

        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (str_starts_with($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);
            // 1. Generate a filename.
            $filename = md5($value . time()) . '.jpg';
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());
            // 3. Save the path to the database
            $this->attributes[$attribute_name] = $destination_path . '/' . $filename;
        }
    }
    public function EditSponsor($crud)
    {
        return '<a class="btn btn-sm btn-link" href="' . url('admin/sponsor/' . $this->sponsor_id . '/edit') . '" id="' . $this->child_id . '" "><i class="la la-edit"></i>Edit2</a>';
    }

    public function donationHistory($crud)
    {
        return '<a class="btn btn-sm btn-link" href="' . url('admin/data-order?sponsor_id=' . $this->sponsor_id) . ' "><i class="la la-hourglass"></i>Donation History</a>';
    }
}
