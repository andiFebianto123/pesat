@extends(backpack_view('blank'))
@section('content')
</br></br>
<h1>Dashboard</h1>

</br>
    <div class="card" style="width:35%">
		<div class="card-header">
        Dashboard Summary        
        </div>
		<div class="card-body">
            <table>
                <tr>
                    <td>
                        Jumlah Anak yang sudah disponsori
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                   {{$sponsored}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Anak yang belum disponsori
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                   {{$notsponsored}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Jumlah Uang
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                     Rp. {{number_format($totalamount, 2, ',', '.')}}   
                    </td>
                </tr>
                <tr>
                    <td>
                        Jumlah Sponsor Baru Bulan ini
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        {{$newsponsor}}
                    </td>
                </tr>

                <tr>
                    <td>
                        Sponsor Yang Belum Bayar
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        {{$notpaid}}
                    </td>
                </tr>

            </table>
        </div>
	</div>
@endsection

