<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
//use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\VarDumper\Cloner\Data;
use App\Traits\CustomRevisionableTrait;

class DataOrder extends Model
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
    protected $table = 'order_hd';
    protected $primaryKey = 'order_id';
    // public $timestamps = false;
    protected $guarded = ['order_id'];
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
    public function sponsorname()
    {
        return $this->belongsTo(Sponsor::class, 'sponsor_id', 'sponsor_id');
    }

    public function childname()
    {
        return $this->belongsTo(ChildMaster::class, 'child_id', 'child_id');
    }


    public function orderdetails()
    {
        return $this->hasMany(DataDetailOrder::class, 'order_id', 'order_id');
    }

    public function oneorderdetail()
    {
        return $this->hasOne(DataDetailOrder::class, 'order_id', 'order_id');
    }

    public function orders()
    {
        return $this->orderdt()->belongsTo(ChildMaster::class, 'child_id', 'child_id');
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
    public function sponsoredchild($crud)
    {
        return '<a class="btn btn-sm btn-link" href="' . url('admin/detail-order/' . $this->order_id . '/detail') . '" id="' . $this->child_id . '" "><i class="la la-list"></i> Detail Order</a>';
    }

    public function cekStatus()
    {
        return '<a class="btn btn-sm btn-link" href="' . backpack_url('child-cek-status/' . $this->order_id) . '" id="' . $this->order_id . '" "><i class="la la-ticket"></i> Cek Status</a>';
    }

    public function cancelOrder()
    {
        if ($this->payment_status == 3) {
            return;
        }
        return '<a class="btn btn-sm btn-link" href="' . backpack_url('child-cancel-order/' . $this->order_id) . '" id="' . $this->order_id . '" "><i class="la la-close"></i> Cancel</a>';
    }
}
