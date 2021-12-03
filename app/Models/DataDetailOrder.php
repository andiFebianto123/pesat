<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Venturecraft\Revisionable\RevisionableTrait;

class DataDetailOrder extends Model
{
    use CrudTrait;

    use RevisionableTrait;
    protected  $revisionForceDeleteEnabled = true;
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    use SoftDeletes;
    protected $table = 'order_dt';
    protected $primaryKey = 'order_dt_id';
    // public $timestamps = false;
    protected $guarded = ['order_dt_id'];
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
    public function childname()
    {
       return $this->belongsTo(ChildMaster::class,'child_id','child_id');
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
}
