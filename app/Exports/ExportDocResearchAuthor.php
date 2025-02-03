<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\DocResearchAuthor;

class ExportDocResearchAuthor implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $implementationYear;


    public function collection()
    {
        return DocResearchAuthor::where('implementation_year', $this->implementationYear)->get();
    }

    public function headings(): array
    {
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
            'partner_leader_name',
            'partner_member1',
            'partner_member2',
            'partner_member3',
            'partner_member4',
            'student_thesis_title',
            'subject_title',
            'funds_total',
            'fund_category',
            'tkt',
            'sdgs_id',
            'created_at',
            'updated_at'
        ];
    }

    public function __construct($implementationYear)
    {
        $this->implementationYear = $implementationYear;
    }

}
