<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    use HasFactory;

    protected $table = 'articles';
    protected $primarykey = 'id';
    protected $fillable=[
        'title',
        'input_date',
        'status_publish',
        'news_content',
        'author',
        'thumbnail_image'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'author', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(ContentCategory::class, 'article_category_mapping','article_id','category_id');
    }


}
