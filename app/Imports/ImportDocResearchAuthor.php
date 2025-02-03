<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\DocResearchAuthor;
use App\Models\GrantSDG;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class ImportDocResearchAuthor implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    *
    * @return \Illuminate\Support\Collection
    */
    public function collection(Collection $rows)
    {
        $validationResults = [];
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
    
            $docResearchAuthor = DocResearchAuthor::where('id', $row['id'])->first();
    
            if ($docResearchAuthor) {
                $docResearchAuthor->update([
                    'student_thesis_title' => $row['judul_ta_pa_mahasiswa'],
                    'fund_category' => $row['kategori'],
                    'tkt' => $row['tkt'],
                ]);
            }
    
            GrantSDG::where('grant_id', $row['id'])->delete();

            $cleanSdgs = str_replace('.', ',', $row['sdgs']);
            $sdgsIds = array_filter(array_map('trim', explode(',', $cleanSdgs)));
            
            foreach ($sdgsIds as $sdgsId) {
                $sdgsId = trim($sdgsId);
                if (!empty($sdgsId)) {
                    
                    GrantSDG::create([
                        'grant_category_id' => 1,
                        'grant_id' => $row['id'],
                        'sdgs_id' => $sdgsId,
                    ]);
                }
            }
            $maxRows--;
        }
    }
    
    private function validateData($row) {
        $title = DocResearchAuthor::where('id', $row['id'])->value('title');

        $validator = Validator::make($row->toArray(), [
            'id' => 'required',
            'judul_ta_pa_mahasiswa' => 'required',
            'kategori' => 'required',
            'tkt' => 'required',
            'sdgs' => 'required',
        ]);
        
        if ($validator->fails()) {
            return [
                'id' => $row['id'],
                'title' => $title,
                'status' => 'Format tidak sesuai',
            ];
        }
    
        return [
            'id' => $row['id'],
            'title' => $title,
            'status' => 'Data sudah sesuai',
        ];
    }
}
