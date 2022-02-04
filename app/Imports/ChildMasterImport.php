<?php

namespace App\Imports;

use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use App\Models\ChildMaster;
use App\Models\Religion;
use App\Models\City;
use App\Models\Province;
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



// HeadingRowFormatter::default('none');

// OnEachRow
class ChildMasterImport implements OnEachRow, WithHeadingRow //WithValidation //SkipsOnFailure
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
        }else{
            $row = $this->convertRowData($dataRow, $rowIndex);
            if($row['id'] == 0){
                // maka dia buat data baru
                $anak = new ChildMaster;
                $anak->registration_number = $row['no_induk'];
                $anak->full_name = $row['n_a_m_a'];
                $anak->nickname = $row['panggilan'];
                $anak->gender = $row['s'];
                $anak->hometown = $row['tpt_lahir'];
                $anak->date_of_birth = $this->convertNumbertoDate($row['tgl_lahir']);
                $anak->religion_id = $row['agama'];
                $anak->fc = $row['fc'];
                $anak->price = 0;
                $anak->sponsor_name = $row['sponsor'];
                $anak->city_id = $row['kabupaten'];
                $anak->districts = $row['kecamatan'];
                $anak->province_id = $row['propinsi'];
                $anak->father = $row['ayah'];
                $anak->mother = $row['ibu'];
                $anak->profession = $row['pekerjaan'];
                $anak->economy = $row['eko'];
                $anak->class = $row['kelas'];
                $anak->school = $row['sekolah'];
                $anak->school_year = $row['an'];
                $anak->sign_in_fc = ($row['masuk_fc'] === null) ? null : $this->convertNumbertoDate($row['masuk_fc']);
                $anak->leave_fc = ($row['keluar_fc'] === null) ? null : $this->convertNumbertoDate($row['keluar_fc']);
                $anak->reason_to_leave = $row['alasan_keluar'] ?? null;
                $anak->child_discription = $row['keterangan'] ?? null;
                $anak->internal_discription = null;
                $anak->status_dlp = 0;
                $anak->created_by = backpack_auth()->user()->id;
                // $anak->is_active = 1;
                $anak->save();
            }else{
                // maka dia update data lama
                $anak = ChildMaster::find($row['id']);
                $anak->registration_number = $row['no_induk'];
                $anak->full_name = $row['n_a_m_a'];
                $anak->nickname = $row['panggilan'];
                $anak->gender = $row['s'];
                $anak->hometown = $row['tpt_lahir'];
                $anak->date_of_birth = $this->convertNumbertoDate($row['tgl_lahir']);
                $anak->religion_id = $row['agama'];
                $anak->fc = $row['fc'];
                $anak->price = 0;
                $anak->sponsor_name = $row['sponsor'];
                $anak->city_id = $row['kabupaten'];
                $anak->districts = $row['kecamatan'];
                $anak->province_id = $row['propinsi'];
                $anak->father = $row['ayah'];
                $anak->mother = $row['ibu'];
                $anak->profession = $row['pekerjaan'];
                $anak->economy = $row['eko'];
                $anak->class = $row['kelas'];
                $anak->school = $row['sekolah'];
                $anak->school_year = $row['an'];
                $anak->sign_in_fc = ($row['masuk_fc'] === null) ? null : $this->convertNumbertoDate($row['masuk_fc']);
                $anak->leave_fc = ($row['keluar_fc'] === null) ? null : $this->convertNumbertoDate($row['keluar_fc']);
                $anak->reason_to_leave = $row['alasan_keluar'] ?? null;
                $anak->child_discription = $row['keterangan'] ?? null;
                $anak->save(); 
            }
        }
    }

  

    private function convertRowData($data, $index)
    {
        $data['s'] = (Str::lower($data['s']) == 'l') ? 'laki-laki' : 'perempuan'; 

        $id = ($data['id'] === null) ? 0 : trim($data['id']);
        $data['id'] = $id;

        if($data['tpt_lahir'] != null){
            // cek tempat lahir terlebih dahulu
            $cekKota = City::where('city_name', trim($data['tpt_lahir']))->limit(1);
            if($cekKota->exists()){
                // jika ada 
                $data['tpt_lahir'] = $cekKota->get()[0]->city_id;
            }
        }

        if($data['agama'] != null){
            // cek agama terlebih dahulu
            $cekData = Religion::where('religion_name', trim($data['agama']))->limit(1);
            if($cekData->exists()){
                // jika ada 
                $data['agama'] = $cekData->get()[0]->religion_id;
            }
        }

        if($data['kabupaten'] != null){
            $cekData = City::where('city_name', trim($data['kabupaten']))->limit(1);
            if($cekData->exists()){
                // jika ada 
                $data['kabupaten'] = $cekData->get()[0]->city_id;
            }
        }

        if($data['propinsi'] != null){
            $cekData = Province::where('province_name', trim($data['propinsi']))->limit(1);
            if($cekData->exists()){
                // jika ada 
                $data['propinsi'] = $cekData->get()[0]->province_id;
            }
        }
        return $data;
    }


    function convertNumbertoDate($str){
        return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($str))
        ->format('Y-m-d');
    }

    
    public function rules($data): array
    {
        return [
            'id' => [
                'integer',
                'nullable',
                function($attribute, $value, $onFailure){
                    if((strlen($value) > 0)){
                        // jika ada id nya maka akan dilakukan cek
                        if (!ChildMaster::where('child_id', $value)->exists()) {
                            $onFailure('ID of child is not exists to update');
                        }
                    }
                }
            ],
            'n_a_m_a' => 'required',
            'panggilan' => 'required',
            'no_induk' => [
                'required',
                function($attribute, $value, $onFailure) use($data) {
                    $cekNoindux = ChildMaster::where('registration_number', $value)->limit(1);
                    if($cekNoindux->exists()){
                        if($data['id'] === null){   
                            $onFailure("{$attribute} not unique in child_master");
                        }else{
                            $getID = $cekNoindux->get()[0]->child_id;
                            if($getID != trim($data['id'])){
                                $onFailure("{$attribute} not unique in child_master");

                            }
                        }
                    }
                }
            ],
            's' => 'required|in:L,P',
            'tpt_lahir' => [
                'required',
                function($attribute, $value, $onFailure){
                    // jika tempat lahir tidak ada
                    $cekKota = City::where('city_name', trim($value))->limit(1);
                    if(!$cekKota->exists()){
                        // jika ada 
                        $onFailure("{$attribute} is not exists in master city");
                    }
                }
            ],
            'tgl_lahir' => 'required|numeric',
            'agama' => [
                'required',
                function($attribute, $value, $onFailure){
                    $cekData = Religion::where('religion_name', trim($value))->limit(1);
                    if(!$cekData->exists()){
                        // jika ada 
                        $onFailure("{$attribute} is not exists in master religion");
                    }
                }
            ],
            'kecamatan' => 'required',
            'kabupaten' => [
                'required',
                'provinsikabupatenvalidation',
                function($attribute, $value, $onFailure){
                    $cekData = City::where('city_name', trim($value))->limit(1);
                    if(!$cekData->exists()){
                        // jika ada 
                        $onFailure("{$attribute} is not exists in master city");
                    }
                }
            ],
            'propinsi' => [
                'required',
                function($attribute, $value, $onFailure){
                    $cekData = Province::where('province_name', trim($value))->limit(1);
                    if(!$cekData->exists()){
                        // jika ada 
                        $onFailure("{$attribute} is not exists in master province");
                    }
                }
            ],
            'ayah' => 'required',
            'ibu' => 'required',
            'pekerjaan' => 'required',
            'eko' => 'required',
            'kelas' => 'required',
            'an' => 'required', // tahun ajaran
            'sekolah' => 'required',
            'masuk_fc' => 'numeric|nullable',
            'keluar_fc' => 'numeric|nullable',
        ];
    }
    
    public function headingRow(): int
    {
        return 1;
    }
}
