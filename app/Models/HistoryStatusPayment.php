<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryStatusPayment extends Model
{
    use HasFactory;
    protected $table = 'history_status_payment';
    protected $primaryKey = 'history_id';
}
