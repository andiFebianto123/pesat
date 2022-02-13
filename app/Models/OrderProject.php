<?php

namespace App\Models;

use App\Models\ProjectMaster;
use App\Traits\CustomRevisionableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderProject extends Model
{
    use HasFactory;
    use CustomRevisionableTrait;
    protected $revisionCreationsEnabled = true;
    protected $revisionForceDeleteEnabled = true;

    protected $table = 'order_project';
    protected $primaryKey = 'order_project_id';
    protected $fillable = ['sponsor_id', 'project_id', 'price', 'payment_status', 'created_at'];


    public function project(){
        return $this->belongsTo(ProjectMaster::class, 'project_id', 'project_id');
    }
}
