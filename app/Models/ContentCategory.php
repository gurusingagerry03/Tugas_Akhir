<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentCategory extends Model
{
    use HasFactory;

    protected $table = 'content_category';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'type',
    ];
    public function articles()
    {
        return $this->belongsToMany(Articles::class, 'article_category_mapping', 'category_id', 'article_id');
    }

}
