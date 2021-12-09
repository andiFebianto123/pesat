<?php

namespace App\Imports;

use App\Models\ChildMaster;
use Maatwebsite\Excel\Concerns\ToModel;

class ChildMasterImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ChildMaster([
            //
            'registration_number' => $row['registration_number'],
            'full_name'           => $row['full_name'],
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
            'class'               => $row['name'],
            'school'              => $row['name'],
            'school_year'         => $row['name'],
            'sign_in_fc'          => $row['name'],
            'leave_fc'            => $row['name'],
            'reason_to_leave'     => $row['name'],
            'child_discription'   => $row['name'],
            'internal_discription'=> $row['name'],
        ]);
    }
}
