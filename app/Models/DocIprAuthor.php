<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocIprAuthor extends Model
{
    use HasFactory;

    //protected $connection = 'dashboard_inovasi';
    protected $table = 'doc_ipr_author';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'author_id',
        'category',
        'requests_year',
        'requests_number',
        'title',
        'inventor',
        'patent_holder',
        'publication_date',
        'publication_number',
        'filing_date',
        'reception_date',
        'registration_date',
        'registration_number'
    ];

    public function profileAuthor()
    {
        return $this->belongsTo(ProfileAuthor::class, 'author_id', 'id');
    }
}
