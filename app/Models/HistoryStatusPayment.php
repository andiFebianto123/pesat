<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomRevisionableTrait;

class HistoryStatusPayment extends Model
{
    use HasFactory;
    use CustomRevisionableTrait;
    protected $revisionCreationsEnabled = true;
    protected $revisionForceDeleteEnabled = true;

    protected $table = 'history_status_payment';
    protected $primaryKey = 'history_id';
    protected $fillable = ['detail_history', 'status', 'status_midtrans', 'user_id'];
}
