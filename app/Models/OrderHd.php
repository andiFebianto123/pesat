<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Venturecraft\Revisionable\RevisionableTrait;

class OrderHd extends Model
{
    use HasFactory;
    use RevisionableTrait;
    protected $revisionCreationsEnabled = true;
    protected $revisionForceDeleteEnabled = true;

    protected $table = 'order_hd';
    protected $primaryKey = 'order_id';
}
