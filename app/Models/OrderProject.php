<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProject extends Model
{
    use HasFactory;
    protected $table = 'order_project';
    protected $primaryKey = 'order_project_id';
}
