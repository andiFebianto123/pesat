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



// HeadingRowFormatter::default('none');

class ChildMasterImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnFailure
{

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    use Importable, SkipsFailures;

    // public function model(array $row)
    // {
    //     return new ChildMaster([
    //         //
    //         'registration_number' => $row['no_induk'],
    //         'full_name'          => $row['full_name'],
    //         'nickname'            => $row['nickname'],
    //         'gender'              => $row['gender'],
    //         'hometown'            => $row['hometown'],
    //         'date_of_birth'       => $row['date_of_birth'],
    //         'religion_id'         => $row['religion_id'],
    //         'fc'                  => $row['fc'],
    //         'price'               => $row['price'],
    //         'sponsor_name'        => $row['sponsor_name'],
    //         'city_id'             => $row['city_id'],
    //         'districts'           => $row['districts'],
    //         'province_id'         => $row['province_id'],
    //         'father'              => $row['father'],
    //         'mother'              => $row['mother'],
    //         'profession'          => $row['profession'],
    //         'economy'             => $row['economy'],
    //         'class'               => $row['class'],
    //         'school'              => $row['school'],
    //         'school_year'         => $row['school_year'],
    //         'sign_in_fc'          => $row['sign_in_fc'],
    //         'leave_fc'            => $row['leave_fc'],
    //         'reason_to_leave'     => $row['reason_to_leave'],
    //         'child_discription'   => $row['child_discription'],
    //         'internal_discription'=> $row['internal_discription'],
    //         'created_by'          => backpack_user()->id,
    //     ]);
        
    // }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();

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
            $anak->reason_to_leave = $row['alasan_keluar'];
            $anak->child_discription = $row['keterangan'];
            $anak->internal_discription = null;
            $anak->status_dlp = 0;
            $anak->is_sponsored = 0;
            $anak->is_paid = 0;
            $anak->current_order_id = null;
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
            $anak->reason_to_leave = $row['alasan_keluar'];
            $anak->child_discription = $row['keterangan'];
            $anak->save();
            
        }
    
    }

    public function prepareForValidation($data, $index)
    {
        $data['s'] = (Str::lower($data['s']) == 'l') ? 'laki-laki' : 'perempuan'; 

        $id = ($data['id'] === null) ? 0 : trim($data['id']);
        $data['id'] = $id;

        if($data['tpt_lahir'] != null){
            // cek tempat lahir terlebih dahulu
            $cekKota = City::where('city_name', 'like', '%'.trim($data['tpt_lahir']).'%')->limit(1);
            if($cekKota->exists()){
                // jika ada 
                $data['tpt_lahir'] = $cekKota->get()[0]->city_id;
            }else{
                $data['tpt_lahir'] = 0;
            }
        }

        if($data['agama'] != null){
            // cek agama terlebih dahulu
            $cekData = Religion::where('religion_name', 'like', '%'.trim($data['agama']).'%')->limit(1);
            if($cekData->exists()){
                // jika ada 
                $data['agama'] = $cekData->get()[0]->religion_id;
            }else{
                $data['agama'] = 0;
            }
        }

        if($data['kabupaten'] != null){
            $cekData = City::where('city_name', 'like', '%'.trim($data['kabupaten']).'%')->limit(1);
            if($cekData->exists()){
                // jika ada 
                $data['kabupaten'] = $cekData->get()[0]->city_id;
            }else{
                $data['kabupaten'] = 0;
            }
        }

        if($data['propinsi'] != null){
            $cekData = Province::where('province_name', 'like', '%'.trim($data['propinsi']).'%')->limit(1);
            if($cekData->exists()){
                // jika ada 
                $data['propinsi'] = $cekData->get()[0]->province_id;
            }else{
                $data['propinsi'] = 0;
            }
        }
        return $data;
    }


    function convertNumbertoDate($str){
        return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($str))
        ->format('Y-m-d');
    }

    
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'integer',
                function($attribute, $value, $onFailure){
                    if((strlen($value) > 0) && ($value != 0)){
                        // jika ada id nya maka akan dilakukan cek
                        if (!ChildMaster::where('child_id', $value)->exists()) {
                            $onFailure('ID of child is not exists to update');
                        }
                    }
                }
            ],
            'n_a_m_a' => 'required',
            'panggilan' => 'required',
            'no_induk' => 'required',
            's' => 'required',
            'tpt_lahir' => [
                'required',
                'integer',
                function($attribute, $value, $onFailure){
                    if($value == 0){
                        // jika tempat lahir tidak ada
                        $onFailure("{$attribute} is not exists in master city");
                    }
                }
            ],
            'tgl_lahir' => 'required|numeric',
            'agama' => [
                'required',
                'integer',
                function($attribute, $value, $onFailure){
                    if($value == 0){
                        $onFailure("{$attribute} is not exists in master religion");
                    }
                }
            ],
            'kecamatan' => 'required',
            'kabupaten' => [
                'required',
                'integer',
                function($attribute, $value, $onFailure){
                    if($value == 0){
                        $onFailure("{$attribute} is not exists in master city");
                    }
                }
            ],
            'propinsi' => [
                'required',
                'integer',
                function($attribute, $value, $onFailure){
                    if($value == 0){
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
}
