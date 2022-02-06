<?php

namespace App\Imports;

use Exception;
use Carbon\Carbon;
use App\Models\City;
use App\Models\Sponsor;
use App\Models\Province;
use App\Models\Religion;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
// 
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;

// 
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
//
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SponsorMasterImport implements OnEachRow, WithHeadingRow, WithMultipleSheets
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

        $sponsor = Sponsor::where('sponsor_id', $row['kode'] ?? null)->first();

        if (empty($sponsor)) {
            // maka dia buat data baru
            $sponsor = new Sponsor;
        }

        $sponsor->sponsor_id = $row['kode'];
        $sponsor->full_name = $row['n_a_m_a'];
        $sponsor->name = $row['n_a_m_a'];
        $sponsor->hometown = $row['tpt_lahir'] ?? null;
        $sponsor->date_of_birth = ($row['tgl_lahir'] === null ? null : $this->convertNumbertoDate($row['tgl_lahir']));
        $sponsor->address = $row['alamat'] ?? null;
        $sponsor->no_hp = $row['handphone'];
        $sponsor->church_member_of = $row['gereja'] ?? null;
        $sponsor->email = $row['email'];
        //generated password test 
        $sponsor->password = bcrypt('pesat');
        $sponsor->save();
    }

    function convertNumbertoDate($str){
        try{
            $date = \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($str))
            ->format('Y-m-d');
            return $date;
        }
        catch(Exception $e){
            return null;
        }
    }

    public function rules($data): array
    {
        return [
            'kode' => ['nullable', 'integer', function($attribute, $value, $onFailure){
                if((strlen($value) > 0)){
                    // jika ada id nya maka akan dilakukan cek
                    if (!Sponsor::where('sponsor_id', $value)->exists()) {
                        $onFailure('ID of sponsor is not exists to update');
                    }
                }
            }],
            'n_a_m_a' => 'required|max:255',
            // 'status' => 'required',
            'tpt_lahir' => 'nullable|max:255',
            'tgl_lahir' => 'nullable|numeric',
            'alamat' => 'nullable|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('sponsor_master')->ignore(($data['kode'] ?? null), 'sponsor_id')],
            'handphone' => 'required|max:255',
            'gereja' => 'nullable|max:255',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
}
