<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProject extends Model
{
    use HasFactory;
    protected $table = 'order_project';
    protected $primaryKey = 'order_project_id';
    protected $fillable = ['sponsor_id', 'project_id', 'price', 'payment_status', 'created_at'];
}
