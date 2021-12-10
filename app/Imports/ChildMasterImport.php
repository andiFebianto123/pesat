<?php

namespace App\Imports;

use App\Models\ChildMaster;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class ChildMasterImport implements ToModel, WithHeadingRow, WithValidation,SkipsEmptyRows
{

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    use Importable;

    public function model(array $row)
    {


        return new ChildMaster([
            //
            'registration_number' => $row['no_induk'],
             'full_name'          => $row['full_name'],
            'nickname'            => $row['nickname'],
            'gender'              => $row['gender'],
            'hometown'            => $row['hometown'],
            'date_of_birth'       => $row['date_of_birth'],
            'religion_id'         => $row['religion_id'],
            'fc'                  => $row['fc'],
            'price'               => $row['price'],
            'sponsor_name'        => $row['sponsor_name'],
            'city_id'             => $row['city_id'],
            'districts'           => $row['districts'],
            'province_id'         => $row['province_id'],
            'father'              => $row['father'],
            'mother'              => $row['mother'],
            'profession'          => $row['profession'],
            'economy'             => $row['economy'],
            'class'               => $row['class'],
            'school'              => $row['school'],
            'school_year'         => $row['school_year'],
            'sign_in_fc'          => $row['sign_in_fc'],
            'leave_fc'            => $row['leave_fc'],
            'reason_to_leave'     => $row['reason_to_leave'],
            'child_discription'   => $row['child_discription'],
            'internal_discription'=> $row['internal_discription'],
            'created_by'          => backpack_user()->id,
        ]);
        
    }

    
    public function rules(): array
    {
        return [

            '*.registration_number' => 'unique:child_master',
        ];
    }
}
