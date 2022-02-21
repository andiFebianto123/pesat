<?php

namespace App\Imports;

use Exception;
use App\Models\City;
use App\Models\Province;
use App\Models\Religion;
use Maatwebsite\Excel\Row;
use App\Models\ChildMaster;
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
use Carbon\Carbon;


// HeadingRowFormatter::default('none');

// OnEachRow
class ChildMasterImport implements OnEachRow, WithHeadingRow, WithMultipleSheets
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

        try {
            $row = $this->convertRowData($row->toArray(), $rowIndex);

            $anak = ChildMaster::where('child_id', $row['id'] ?? null)->first();

            if (empty($anak)) {
                // maka dia buat data baru
                $anak = new ChildMaster;
                $anak->child_id = $row['id'];
            }

            $anak->registration_number = $row['no_induk'];
            $anak->full_name = $row['title'];
            $anak->nickname = $row['panggilan'];
            $anak->gender = $row['jenis_kelamin'];
            $anak->hometown = $row['tempat_lahir'];
            $anak->date_of_birth = Carbon::parse($row['tanggal_lahir']);
            $anak->fc = $row['fc'];
            $anak->price = 150000;
            $anak->city_id = $row['kabupaten'];
            $anak->districts = $row['kecamatan'];
            $anak->religion_id = $row['agama'];
            $anak->province_id = $row['propinsi'];
            $anak->father = $row['ayah'];
            $anak->mother = $row['ibu'];
            $anak->profession = $row['pekerjaan'];
            $anak->economy = $row['ekonomi'];
            $anak->class = $row['kelas'];
            $anak->school = $row['sekolah'];
            $anak->school_year = $row['th_ajaran'];
            $anak->sign_in_fc = ($row['masuk_fc'] === null) ? null : Carbon::parse($row['masuk_fc']);
            $anak->leave_fc = ($row['keluar_fc'] === null) ? null : Carbon::parse($row['keluar_fc']);
            $anak->reason_to_leave = $row['alasan_keluar'] ?? null;
            $anak->child_discription = $row['keterangan'] ?? null;
            $anak->internal_discription = null;
            $anak->status_dlp = 0;
            $anak->created_by = backpack_auth()->user()->id;
            if ($row['image_url'] != null) {
                $anak->setPhotoProfileAttribute($row['image_url'], true);
            }

            //$anak->is_active = 1;
            $anak->save();
        } catch (Exception $e) {
            $this->errorsMessage[] = ['row' => $rowIndex, 'message' => $e->getMessage()];
        }
    }


    private function convertRowData($data, $index)
    {

        if ($data['tempat_lahir'] != null) {
            // cek tempat lahir terlebih dahulu
            $kabupaten = trim($data['tempat_lahir']);
            $cekKota = City::where('city_name', 'LIKE', "%{$kabupaten}%")->limit(1);
            if ($cekKota->exists()) {
                // jika ada 
                $data['tempat_lahir'] = $cekKota->get()[0]->city_id;
            } else {
                $data['tempat_lahir'] = null;
            }
        }

        if ($data['agama'] != null) {
            // cek agama terlebih dahulu
            $cekData = Religion::where('religion_name', trim($data['agama']))->limit(1);
            if ($cekData->exists()) {
                // jika ada 
                $data['agama'] = $cekData->get()[0]->religion_id;
            } else {
                $data['agama'] = null;
            }
        }

        if ($data['kabupaten'] != null) {
            $kabupaten = trim($data['kabupaten']);
            $cekData =  City::where('city_name', 'LIKE', "%{$kabupaten}%")->limit(1);
            if ($cekData->exists()) {
                // jika ada 
                $data['kabupaten'] = $cekData->get()[0]->city_id;
            } else {
                $data['kabupaten'] = null;
            }
        }

        if ($data['propinsi'] != null) {
            $provinsi = trim($data['propinsi']);
            $cekData = Province::where('province_name', 'LIKE', "%{$provinsi}%")->limit(1);
            if ($cekData->exists()) {
                // jika ada 
                $data['propinsi'] = $cekData->get()[0]->province_id;
            } else {
                $data['propinsi'] = null;
            }
        }

        if ($data['tanggal_lahir']) {
            $birthday = explode("-", $data['tanggal_lahir']);
            if (count($birthday) == 3) {
                switch ($birthday[1]) {
                    case "Mei":
                        $birthday[1] = "May";
                        break;
                    case "Ags":
                        $birthday[1] = "Aug";
                        break;
                    case "Okt":
                        $birthday[1] = "Oct";
                        break;
                    case "Des":
                        $birthday[1] = "Dec";
                        break;
                }

                $birthday = join("-", $birthday);

                $data['tanggal_lahir'] = $birthday;
            }
        }

        if ($data['masuk_fc']) {
            $enterFc = explode("-", $data['masuk_fc']);
            if (count($enterFc) == 3) {
                switch ($enterFc[1]) {
                    case "Mei":
                        $enterFc[1] = "May";
                        break;
                    case "Ags":
                        $enterFc[1] = "Aug";
                        break;
                    case "Okt":
                        $enterFc[1] = "Oct";
                        break;
                    case "Des":
                        $enterFc[1] = "Dec";
                        break;
                }

                $enterFc = join("-", $enterFc);

                $data['masuk_fc'] = $enterFc;
            }
        }

        if ($data['keluar_fc']) {
            $keluarFc = explode("-", $data['keluar_fc']);
            if (count($keluarFc) == 3) {
                switch ($keluarFc[1]) {
                    case "Mei":
                        $keluarFc[1] = "May";
                        break;
                    case "Ags":
                        $keluarFc[1] = "Aug";
                        break;
                    case "Okt":
                        $keluarFc[1] = "Oct";
                        break;
                    case "Des":
                        $keluarFc[1] = "Dec";
                        break;
                }

                $keluarFc = join("-", $keluarFc);

                $data['keluar_fc'] = $keluarFc;
            }
        }

        return $data;
    }


    public function rules($data): array
    {
        return [
            'id' => 'required|integer',
            'title' => 'required|max:255',
            'panggilan' => 'string|nullable',
            'no_induk' => 'required|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'string|nullable',
            'agama' => 'string|nullable',
            'tanggal_lahir' => 'string|nullable',
            'kecamatan' => 'required|max:255',
            'kabupaten' => 'required|string',
            'propinsi' => 'string|nullable',
            'ayah' => 'string|nullable',
            'ibu' => 'string|nullable',
            'pekerjaan' => 'string|nullable',
            'ekonomi' => 'nullable',
            'kelas' => 'required|max:255',
            'th_ajaran' => 'required|max:255', // tahun ajaran
            'sekolah' => 'required|max:255',
            'masuk_fc' => 'string|nullable',
            'keluar_fc' => 'string|nullable',
            'image_url' => 'string|nullable',
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
