<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'contact_us';
    protected $fillable = [
        'grant_collaborator_category_id',
        'input_date',
        'sender_name',
        'sender_institution',
        'contact_number',
        'type',
        'subject',
        'content',
        'status'
    ];

    public function grantCollaboratorCategory()
    {
        return $this->belongsTo(GrantCollaboratorCategory::class, 'grant_collaborator_category_id', 'id');
    }
}
