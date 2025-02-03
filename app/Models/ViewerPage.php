<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewerPage extends Model
{
    use HasFactory;

    protected $table = 'viewer_page';

    protected $fillable = [
        'id',
        'page_id',
        'access_date',
    ];

    public function webPage()
    {
        return $this->belongsTo(WebPage::class, 'page_id', 'id');
    }

}
