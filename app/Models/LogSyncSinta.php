<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSyncSinta extends Model
{
    use HasFactory;

    protected $table = "log_sync_sinta";

    protected $fillable = [
        'username',
        'endpoint',
        'status',
    ];
}