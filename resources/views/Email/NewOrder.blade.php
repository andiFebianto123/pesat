<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>

    <style type="text/css">
        h1 {
            text-align: center;
            }
        table, td, th {
            border: 1px solid black;
            text-align : center;
            }

        table {
            width: 80%; 
            border-collapse: collapse;
            }
    </style>
</head>
<body>

<h1>{{$title}}</h1>
<div class="container">
<div class="row">
    <div class="col-1">
    <pre>Nama 		: {{$sponsor_name}}                      Tanggal: {{$date_now}}</pre>
    <pre>Telepon	: {{$no_hp}}					        	        Order ID : {{$order_id}}</pre>
    <pre>Alamat		: {{$sponsor_address}}</pre>
        </br>
        </br>
        <table>
            <tr>
                <th>Nama Anak</th>
                <th>Durasi Subscribe</th>
                <th>Total</th>
            </tr>
            <tr>
                <td>{{$child_name}}</td>
                <td>{{$monthly_subscription}}</td>
                <td>Rp. {{$price}}</td>
            </tr>
        </table>
    
    </div>

</div>
</div>    
    <p>Thank you</p>
</body>
</html>