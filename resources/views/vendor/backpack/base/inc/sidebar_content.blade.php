<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('child-master') }}'><i class='nav-icon la la-user'></i> Data Anak</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('project-master') }}'><i class='nav-icon la la-industry'></i> Data Proyek</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('users') }}'><i class='nav-icon la la-user-plus'></i> Users</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Master Data</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('province') }}'><i class='nav-icon la la-building'></i> Provinsi</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('city') }}'><i class='nav-icon la la-university'></i> Kota/Kabupaten</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('religion') }}'><i class='nav-icon la la-venus-double'></i> Agama</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sponsor-type') }}'><i class='nav-icon la la-diamond'></i> Sponsor types</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('user-role') }}'><i class='nav-icon la la-gears'></i> User roles</a></li>
    </ul>
</li>
<!-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('project-master-detail') }}'><i class='nav-icon la la-question'></i> Project master details</a></li> -->
<!-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('dlp') }}'><i class='nav-icon la la-question'></i> Dlp</a></li> -->

