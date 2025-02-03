<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\DocCommunityserviceAuthor;

class GrantSDG extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'grant_sdgs';

    protected $fillable = [
        'grant_category_id',
        'grant_id',
        'sdgs_id',
    ];

    protected $appends = ['category_name', 'grant'];
    protected $hidden = ['community', 'research'];

    public function community()
    {
        return $this->belongsTo(DocCommunityserviceAuthor::class, 'grant_id', 'id')->with('members');
    }

    public function research()
    {
        return $this->belongsTo(DocResearchAuthor::class, 'grant_id', 'id')->with('members');
    }

    public function sdg(): HasMany
    {
        return $this->hasMany(SDG::class, 'id', 'sdgs_id');
    }

    public function getCategoryNameAttribute()
    {
        $categories = [
            1 => 'Riset',
            2 => 'Abdimas',
        ];

        return $categories[$this->grant_category_id] ?? 'Unknown';
    }

    public function getGrantAttribute()
    {
        if ($this->grant_category_id === 1) {
            return $this->research;
        } elseif ($this->grant_category_id === 2) {
            return $this->community;
        }

        return null;
    }
}
