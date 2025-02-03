<?php

namespace App\Exports;

use App\Models\DocCommunityserviceAuthor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportDocCommunityServiceAuthor implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $implementationYear;

    public function collection()
    {
        return DocCommunityserviceAuthor::where('implementation_year', $this->implementationYear)->get();
    }

    public function headings():array{
        return [
            'id',
            'author_id',
            'leader',
            'leader_nidn',
            'title',
            'first_proposed_year',
            'proposed_year',
            'implementation_year',
            'focus',
            'funds_approved',
            'scheme_name',
            'scheme_abbrev',
            'tkt',
            'result_comservice',
            'target_society_name',
            'target_society_address',
            'target_society_cityorregancy',
            'created_at',
            'updated_at'
        ];
    }
    public function __construct($implementationYear)
    {
        $this->implementationYear = $implementationYear;
    }
}
