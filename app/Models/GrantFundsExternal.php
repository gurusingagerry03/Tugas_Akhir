<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GrantFundsExternal extends Model
{
    use HasFactory;

    protected $primaryKey='id';
    protected $table = 'grant_funds_external';

    protected $fillable=[
        'grant_category_id',
        'grant_id',
        'collaborator_name',
        'collaborator_category_id',
        'funds_approved',
        'funds_category',
        'funds_program_name'
    ];

    protected $appends = ['category_name'];
    
    public function collaboratorCategory(): HasOne
    {
        return $this->hasOne(GrantCollaboratorCategory::class, 'id', 'collaborator_category_id');
    }
    
    public function getCategoryNameAttribute()
    {
        $categories = [
            1 => 'Riset',
            2 => 'Abdimas',
        ];
        
        return $categories[$this->grant_category_id] ?? 'Unknown';
    }
}
