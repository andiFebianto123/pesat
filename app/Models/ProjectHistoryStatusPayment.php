<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectHistoryStatusPayment extends Model
{
    use HasFactory;
    protected $table = 'project_history_status_payment';
    protected $primaryKey = 'history_id';
    protected $fillable = ['detail_history', 'status', 'status_midtrans', 'user_id'];
}
