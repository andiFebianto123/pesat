<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomRevisionableTrait;

class OrderProject extends Model
{
    use HasFactory;
    use CustomRevisionableTrait;
    protected $revisionCreationsEnabled = true;
    protected $revisionForceDeleteEnabled = true;

    protected $table = 'order_project';
    protected $primaryKey = 'order_project_id';
    protected $fillable = ['sponsor_id', 'project_id', 'price', 'payment_status', 'created_at'];
}
