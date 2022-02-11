<?php

namespace App\Models;

use App\Models\DataDetailOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Traits\CustomRevisionableTrait;

class ChildMaster extends Model
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
  protected $table = 'child_master';
  protected $primaryKey = 'child_id';
  // public $timestamps = false;
  protected $guarded = ['child_id'];
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
  public function sponsor_type()
  {
    return $this->belongsTo(SponsorType::class, 'sponsor_type_id', 'sponsor_type_id');
  }
  public function city()
  {
    return $this->belongsTo(City::class, 'hometown', 'city_id');
  }
  public function religion()
  {
    return $this->belongsTo(Religion::class, 'religion_id', 'religion_id');
  }
  public function city2()
  {
    return $this->belongsTo(City::class, 'city_id', 'city_id');
  }
  public function province()
  {
    return $this->belongsTo(Province::class, 'province_id', 'province_id');
  }
  public function dlp()
  {
    return $this->belongsTo(Dlp::class, 'child_id', 'child_id');
  }
  public function users()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
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
  public function setFileProfileAttribute($value)
  {
    $attribute_name = "file_profile";
    $disk           = "public";
    $destination_path = "uploads";
    $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
  }


  public function DetailDlp($crud)
  {
    $link = "add-dlp";
    return '<a class="btn btn-sm btn-link" href="' . url('admin/dlp/' . $this->child_id . '/detail') . '" id="' . $this->child_id . '" "><i class="la la-file"></i> DLP</a>';
  }

  public function getSlugWithLink()
  {
    return '<a href="' . url($this->slug) . '" target="_blank">' . $this->slug . '</a>';
  }

  public function detailorders()
  {
    return $this->hasMany(DataDetailOrder::class, 'child_id', 'child_id');
  }

  public static function getStatusSponsor($child_id, $now, $returnId = false)
  {
    $cekStatusPayment = DataDetailOrder::where('child_id', $child_id)
      ->whereDate('start_order_date', '<=', $now)
      ->whereDate('end_order_date', '>=', $now)
      ->join('order_hd', 'order_hd.order_id', 'order_dt.order_id')
      ->where('order_hd.payment_status', '<=', 2)
      ->select('order_hd.order_id')
      ->first();

    if ($returnId) {
      return $cekStatusPayment->order_id ?? null;
    }

    return $cekStatusPayment != null;
  }
}
