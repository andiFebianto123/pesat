<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
//use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\VarDumper\Cloner\Data;
use \Venturecraft\Revisionable\RevisionableTrait;

class DataOrder extends Model
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
       return $this->belongsTo(Sponsor::class,'sponsor_id','sponsor_id');
    }

    public function childname()
    {
       return $this->belongsTo(ChildMaster::class,'child_id','child_id');
    }


    public function childnamenosponsor()
    {
       return $this->belongsTo(ChildMaster::class,'child_id','child_id')->where('is_sponsored',0);
    }







    public function orderdt()
    {
        return $this->belongsTo(DataDetailOrder::class,'order_id','order_id');
    }
    public function orders()
    {
        return $this->orderdt()->belongsTo(ChildMaster::class,'child_id','child_id');
        
    }

    public function test()
	{
        return $this->orders()->where('is_sponsored',false);
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
        return '<a class="btn btn-sm btn-link" href="'.url('admin/detail-sponsor/'.$this->order_id.'/detail').'" data-toggle="tooltip" title="Just a demo custom button." id="'.$this->child_id.'" "><i class="la la-file"></i> Sponsored Child</a>';
    }

    public function Cek_Status(){
        return '<a class="btn btn-sm btn-link" href="'.backpack_url('child-cek-status/'.$this->order_id).'" data-toggle="tooltip" title="Just a demo custom button." id="'.$this->order_id.'" "><i class="fa fa-search"></i> Cek Status</a>';
       
      }

      public function cancelOrder(){
        return '<a class="btn btn-sm btn-link" href="'.backpack_url('child-cancel-order/'.$this->order_id).'" data-toggle="tooltip" title="Just a demo custom button." id="'.$this->order_id.'" "><i class="fa fa-search"></i> Cancel</a>';
       
      }
    
}
