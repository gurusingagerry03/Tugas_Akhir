<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GrantIdExists implements Rule
{
    public function passes($attribute, $value)
    {
        $existsInFirstTable = DB::table('doc_communityservice_author')->where('id', $value)->exists();
        $existsInSecondTable = DB::table('doc_research_author')->where('id', $value)->exists();

        return $existsInFirstTable || $existsInSecondTable;
    }

    public function message()
    {
        return 'The selected grant ID is invalid.';
    }
}
