<?php

namespace App\Imports;

use App\Models\DocCommunityserviceAuthor;
use App\Models\GrantSDG;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Validator;

class ImportDocCommunityServiceAuthor implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */

    public $validationResults = [];

    public function collection(Collection $rows)
    {
        
        $allValid = true;
        $maxRows = 50;
    
        foreach ($rows as $row) {
            if (empty($row['id'])) {
                break;
            }
    
            $validationResult = $this->validateData($row);
            $validationResults[] = $validationResult;
    
            if ($validationResult['status'] !== 'Data sudah sesuai') {
                $allValid = false;
            }
        }
    
        $this->validationResults = $validationResults;
    
        if (!$allValid) {
            return;
        }
    
        foreach ($rows as $row) {
            if ($maxRows == 0) {
                break;
            }
    
            $docCommServiceAuthor = DocCommunityServiceAuthor::where('id', $row['id'])->first();
    
            if ($docCommServiceAuthor) {
                $docCommServiceAuthor->update([
                    'tkt' => $row['tkt']
                ]);
            }
    
            GrantSDG::where('grant_id', $row['id'])->delete();

            $cleanSdgs = str_replace('.', ',', $row['sdgs']);
            $sdgsIds = array_filter(array_map('trim', explode(',', $cleanSdgs)));
            
            foreach ($sdgsIds as $sdgsId) {
                $sdgsId = trim($sdgsId);
                if (!empty($sdgsId)) {
                    
                    GrantSDG::create([
                        'grant_category_id' => 2,
                        'grant_id' => $row['id'],
                        'sdgs_id' => $sdgsId,
                    ]);
                }
            }
            $maxRows--;
        }
    }
    
    private function validateData($row) {
        $title = DocCommunityServiceAuthor::where('id', $row['id'])->value('title');

        $validator = Validator::make($row->toArray(), [
            'id' => 'required',
            'tkt' => 'required',
            'sdgs' => 'required',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors();
            $missingFields = [];

            foreach (['id', 'tkt', 'sdgs'] as $field) {
                if ($errors->has($field)) {
                    $missingFields[] = $field;
                }
            }

            return [
                'id' => $row['id'] ?? null,
                'title' => $title,
                'status' => 'Format ' . implode(', ', $missingFields). ' tidak sesuai',
            ];
        }
    
        return [
            'id' => $row['id'],
            'title' => $title,
            'status' => 'Data sudah sesuai',
        ];
    }
}
