<?php

namespace App\Imports;

use App\Models\Sponsor;
use App\Models\sponsor_master;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;


class SponsorMasterImport implements ToModel, WithHeadingRow//, WithValidation,SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Sponsor([
            //
            'name'            => $row['name'],
            'first_name'      => $row['first_name'],
            'last_name'       => $row['last_name'],
            'full_name'       => $row['full_name'],
            'hometown'        => $row['hometown'],
            'date_of_birth'   => $row['date_of_birth'],
            'address'         => $row['address'],
            'no_hp'           => $row['no_hp'],
            'church_member_of'=> $row['church_member_of'],
            'email'           => $row['email'],
            'password'        => bcrypt( $row['password']),
            'website_url'     => $row['website_url'],
            'facebook_url'    => $row['facebook_url'],
            'instagram_url'   => $row['instagram_url'],
            'linkedin_url'    => $row['linkedin_url'],
            'my_space_url'    => $row['my_space_url'],
            'pinterest_url'   => $row['pinterest_url'],
            'sound_cloud_url' => $row['sound_cloud_url'],
            'pinterest_url'   => $row['pinterest_url'],
            'tumblr_url'      => $row['tumblr_url'],
            'twitter_url'     => $row['twitter_url'],
            'youtube_url'     => $row['youtube_url'],
            'biograpical'     => $row['biograpical'],
            
        ]);
    
    }

}
