<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHd extends Model
{
    use HasFactory;

    protected $table = 'order_hd';
    protected $primaryKey = 'order_id';
}
