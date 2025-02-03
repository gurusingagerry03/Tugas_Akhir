<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewerLecture extends Model
{
    use HasFactory;

    protected $table = 'viewer_lecture';

    protected $fillable = [
        'id',
        'author_id',
        'access_date',
    ];

    public function ProfileAuthor()
    {
        return $this->belongsTo(ProfileAuthor::class, 'author_id', 'id');
    }
}
