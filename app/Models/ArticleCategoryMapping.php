<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleCategoryMapping extends Pivot
{
    use HasFactory;
    protected $table = 'article_category_mapping';
    protected $primaryKey = 'id';

    protected $fillable = [
        'category_id',
        'article_id'
    ];
}
