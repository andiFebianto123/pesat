<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Venturecraft\Revisionable\RevisionableTrait;

class HistoryStatusPayment extends Model
{
    use HasFactory;
    use RevisionableTrait;
    protected $revisionCreationsEnabled = true;
    protected $revisionForceDeleteEnabled = true;

    protected $table = 'history_status_payment';
    protected $primaryKey = 'history_id';
    protected $fillable = ['detail_history', 'status', 'status_midtrans', 'user_id'];
}
