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
    public $pass;

    function __construct()
    {
        $this->pass = bcrypt('pesat');
    }


    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $dataRow  = $row->toArray();
        if (isset($dataRow['tanggal_lahir']) && is_numeric($dataRow['tanggal_lahir'])) {
            try{
                $dataRow['tanggal_lahir'] = $this->formatDateExcel($dataRow['tanggal_lahir']);
            }
            catch(Exception $e){
                $dataRow['tanggal_lahir'] = null;
            }
        }


        $validator = Validator::make($dataRow, $this->rules($dataRow));

        if ($validator->fails()) {
            $this->errorsMessage[] = ['row' => $rowIndex, 'message' => collect($validator->errors()->all())->join('<br />')];
            return;
        }

        try {
            $row = $dataRow;

            $isValid = $this->isValidData($row);

            if (!$isValid) return;

            $row = $this->convertRowData($row, $rowIndex);

            $sponsor = Sponsor::where('sponsor_id', $row['id'] ?? null)->first();

            if (empty($sponsor)) {
                // maka dia buat data baru
                $sponsor = new Sponsor;
                // $sponsor->sponsor_id = $row['id'];
            }

            $sponsor->name = $row['nama'];
            $sponsor->first_name = $row['first_name'] ?? null;
            $sponsor->last_name = $row['last_name'] ?? null;
            $sponsor->full_name = $row['nama'];

            $sponsor->hometown = $row['tempat_lahir'] ?? null;
            $sponsor->date_of_birth = ($row['tanggal_lahir'] === null ? null : Carbon::parse($row['tanggal_lahir']));
            $sponsor->address = $row['alamat'] ?? null;
            $sponsor->no_hp = $row['no_ponsel_whatsapp'] ?? null;
            $sponsor->church_member_of = $row['jemaat_dari_gereja'] ?? null;
            $sponsor->email = $row['user_email'];
            //generated password test 
            $sponsor->password = $this->pass;
            $sponsor->save();
        } catch (Execption $e) {
            $this->errorsMessage[] = ['row' => $rowIndex, 'message' => $e->getMessage()];
        }
    }

    private function isValidData($data)
    {
        $isValid = true;

        if ($data['user_email'] == null) {
            $isValid = false;
        } else if (!filter_var($data['user_email'], FILTER_VALIDATE_EMAIL)) {
            $isValid = false;
        } else {
            $emailExist = Sponsor::where('email', $data['user_email'])->where('sponsor_id', '!=', $data['id'])->first();
            if ($emailExist != null) $isValid = false;
        }
        if (!isset($data['nama'])) {
            $isValid = false;
        }

        return $isValid;
    }



    private function convertRowData($data, $index)
    {
        $name = explode(" ", $data['nama']);
        if (count($name) >= 2) {
            $data['first_name'] = $name[0];
            $data['last_name'] = $name[1];
        }
        
        // if ($data['tempat_lahir'] != null) {
        //     // cek tempat lahir terlebih dahulu
        //     $kabupaten = trim($data['tempat_lahir']);
        //     $cekKota = City::where('city_name', 'LIKE', "%{$kabupaten}%")->limit(1);
        //     if ($cekKota->exists()) {
        //         // jika ada 
        //         $data['tempat_lahir'] = $cekKota->get()[0]->city_id;
        //     } else {
        //         $data['tempat_lahir'] = null;
        //     }
        // }

        // if (isset($data['tanggal_lahir'])) {
            // $birthday = explode("/", $data['tanggal_lahir']);
            // if (count($birthday) == 3) {
            //     $birthday = join("-", $birthday);
            //     $data['tanggal_lahir'] = $birthday;
            // }

            // try {
            //     Carbon::parse($data['tanggal_lahir']);
            // } catch (\Exception $e) {
            //     $data['tanggal_lahir'] = null;
            // }
        // }

        return $data;
    }

    public function rules($data): array
    {
        return [
            'id' => 'nullable|integer',
            'nama' => 'required|max:255',
            // 'status' => 'required',
            'tempat_lahir' => 'nullable|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|max:255',
            'user_email' => 'required|email|max:255',
            'no_ponsel_whatsapp' => 'nullable|max:255',
            'jemaat_dari_gereja' => 'nullable|max:255',
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

    function formatDateExcel($dateExcel)
    {
        return Carbon::createFromTimestamp(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($dateExcel));
    }
}
