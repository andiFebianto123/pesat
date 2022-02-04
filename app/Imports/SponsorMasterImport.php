<?php

namespace App\Imports;

use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use App\Models\Sponsor;
use App\Models\Religion;
use App\Models\City;
use App\Models\Province;
use Exception;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
// 
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

// 
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
//
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;

class SponsorMasterImport implements OnEachRow, WithHeadingRow //WithValidation //SkipsOnFailure
{

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    use Importable; //SkipsFailures;


    public $errorsMessage = [];

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $dataRow  = $row->toArray();

        $validator = Validator::make($dataRow, $this->rules($dataRow));

        if ($validator->fails()) {
            $this->errorsMessage[] = ['row' => $rowIndex, 'message' => collect($validator->errors()->all())->join('<br />')];
            return;
        }

        $sponsor = Sponsor::where('sponsor_id', $row['kode'])->first();

        if (empty($sponsor)) {
            // maka dia buat data baru
            $sponsor = new Sponsor;
        }

        try {
            $sponsor->sponsor_id = $row['kode'];
            $sponsor->full_name = $row['n_a_m_a'];
            $sponsor->name = $row['n_a_m_a'];
            $sponsor->hometown = $row['tpt_lahir'];
            $sponsor->date_of_birth = $row['tgl_lahir'];
            $sponsor->address = $row['alamat'];
            $sponsor->no_hp = $row['handphone'];
            $sponsor->church_member_of = $row['gereja'];
            $sponsor->email = $row['email'];
            //generated password test 
            $sponsor->password = '$2a$12$bY7FLATflatpR2kv9mwh1eawjPSKlHzbc3adBTwSpaDENMseTdtRm';
            $sponsor->save();
        } catch (Exception $e) {
            $this->errorsMessage[] = ['row' => $rowIndex, 'message' => $e->getMessage()];
        }
    }

    public function rules($data): array
    {
        return [
            'kode' => 'required|numeric',
            'n_a_m_a' => 'required',
            'status' => 'required',
            'tpt_lahir' => 'nullable',
            'tgl_lahir' => 'nullable',
            'alamat' => 'nullable',
            'email' => 'required|email',
            'handphone' => 'required',
            'gereja' => 'nullable',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}
