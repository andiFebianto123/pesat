<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChildMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::insert('INSERT INTO child_master (full_name, registration_number,nickname,gender,hometown,date_of_birth,religion_id,price,province_id,city_id,districts,father,mother,profession,economy,class,school,school_year,created_by) VALUES
        ("Andreas Argo  Pratama", "AB001","Andre","Laki-laki",1,"2021-10-01",1,100000,10,212,"Ambarawa","Agustinus","Anis","Swasta","Miskin",7,"SMP",2021,1),
        ("Andi Kurniawan", "AB002","Andi","Laki-laki",1,"2021-10-01",1,150000,3,161,"Kelapa Dua","Agustinus","Anis","Swasta","Miskin",8,"SMP",2021,1),
        ("Yoga Pratama", "AB003","Yoga","Laki-laki",1,"2021-10-01",1,200000,11,262,"Surabaya","Agustinus","Anis","Swasta","Miskin",9,"SMP",2021,1),
        ("Candra Aji", "AB004","Candra","Laki-laki",1,"2021-10-01",1,130000,11,248,"Sidoarjo","Agustinus","Anis","Swasta","Miskin",7,"SMP",2021,1),
        ("Richal Maulana", "AB005","Richal","Laki-laki",1,"2021-10-01",1,120000,1,274,"Badung","Agustinus","Anis","Swasta","Miskin",10,"SMK",2021,1),
        ("Widyastuti", "AB006","Tyas","Perempuan",1,"2021-10-01",1,105000,2,133,"Bangka","Agustinus","Anis","Swasta","Miskin",7,"SMP",2021,1),
        ("Dwi Wulandari", "AB007","Wulan","Perempuan",1,"2021-10-01",1,120000,3,155,"Lebak","Agustinus","Anis","Swasta","Miskin",10,"SMA",2021,1),
        ("Meilia Triyani", "AB008","Mei","Perempuan",1,"2021-10-01",1,250000,4,126,"Kaur","Agustinus","Anis","Swasta","Miskin",9,"SMP",2021,1),
        ("Putri Herawati", "AB009","Putri","Perempuan",1,"2021-10-01",1,210000,6,265,"Rawa Buaya","Agustinus","Anis","Swasta","Miskin",8,"SMP",2021,1),
        ("Putra Agus", "AB010","Putra","Laki-laki",1,"2021-10-01",1,170000,5,268,"Salemba","Agustinus","Anis","Swasta","Miskin",12,"SMK",2021,1)'
        );

    }
    
}
