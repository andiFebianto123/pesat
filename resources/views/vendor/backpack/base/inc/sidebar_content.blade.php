<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('child-master') }}'><i class='nav-icon la la-user'></i> Data Anak</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('project-master') }}'><i class='nav-icon la la-industry'></i> Data Proyek</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('sponsor') }}'><i class='nav-icon la la-book'></i>Data Sponsor</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('data-order') }}'><i class='nav-icon la la-child'></i> Data Order Anak</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('data-order-project') }}'><i class='nav-icon la la-building'></i> Data Order Proyek</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('donate-goods') }}'><i class='nav-icon la la-tablet'></i>Donasi Barang</a></li>
<!-- <li class='nav-item'><a class='nav-link' href="{{ backpack_url('report') }}"><i class='nav-icon la la-book'></i> Report</a></li> -->
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('users') }}'><i class='nav-icon la la-user-plus'></i> Users</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Master Data</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('province') }}'><i class='nav-icon la la-building'></i> Provinsi</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('city') }}'><i class='nav-icon la la-university'></i> Kota/Kabupaten</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('religion') }}'><i class='nav-icon la la-venus-double'></i> Agama</a></li>
        <!-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sponsor-type') }}'><i class='nav-icon la la-diamond'></i> Type Sponsor</a></li> -->
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('user-role') }}'><i class='nav-icon la la-gears'></i> User Role</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('import-anak') }}'><i class='nav-icon la la-male'></i> Import Data Anak</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('import-sponsor') }}'><i class='la la-street-view'></i> Import Data Sponsor</a></li>
    </ul>
</li>
<!-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('project-master-detail') }}'><i class='nav-icon la la-question'></i> Project master details</a></li> -->
<!-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('dlp') }}'><i class='nav-icon la la-question'></i> Dlp</a></li> -->
<!-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('user-attribute') }}'><i class='nav-icon la la-question'></i> User attributes</a></li> -->
<!-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('data-detail-order') }}'><i class='nav-icon la la-question'></i> Data detail orders</a></li> -->
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('config') }}'><i class='nav-icon la la-gear'></i> Config</a></li>