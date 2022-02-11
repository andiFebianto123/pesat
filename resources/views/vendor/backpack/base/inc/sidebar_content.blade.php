<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-database"></i> Master Data</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('child-master') }}'>Data Anak</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('project-master') }}'> Data Proyek</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sponsor') }}'>Data Sponsor</a></li>
    </ul>
</li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('sponsor-donation') }}'><i class='nav-icon la la-user-check'></i> Master Sponsor</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-briefcase"></i> Orders</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('data-order') }}'>Data Order Anak</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('data-order-project') }}'>Data Order Proyek</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('donate-goods') }}'>Donasi Barang</a></li>
    </ul>
</li>
<li class="navitem nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-wrench"></i> Tools</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('import-anak') }}'> Import Anak</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('import-sponsor') }}'> Import Sponsor</a></li>
    </ul>
</li>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('users') }}'><i class='nav-icon la la-user-plus'></i> Users</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-gear"></i> Settings</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('general') }}'>General</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('province') }}'>Provinsi</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('city') }}'>Kota/Kabupaten</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('religion') }}'>Agama</a></li>       
    </ul>
</li>