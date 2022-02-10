<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Receipt #{{$order->order_id}}</title>
        <style>
            @page{
                margin-top:16px !important;
                margin-bottom:16px !important;
            }
            body {
                margin: 0;
                font-family: -apple-system,BlinkMacSystemFont, Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
                font-size: 14px;
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                text-align: left;
                background-color: #fff;
            }
            *, ::after, ::before {
                box-sizing: border-box;
            }
            .container {
                width: 100%;
		    }
            .text-right{
                text-align:right;
            }
            .text-left{
                text-align:left;
            }
            .text-center{
                text-align:center;
            }
            .text-bold{
                font-weight:bold;
            }
            .table td, .table th {
                padding: 0.5rem;
                vertical-align: top;
            }
        </style>
    </head>
    <body class="container">
        @php
            $base64Image = null;
            if(\File::exists(public_path('images/logopesat.png'))){
                $base64Image = base64_encode(file_get_contents(public_path('images/logopesat.png')));
                echo '<img src="data:image/png;base64,' . $base64Image . '" style="width:auto;height:90px;margin-top:16px" />';
            }
        @endphp
        <h2 style="margin-top:32px">{{strtoupper($typePdf)}}</h2>
        @php
            $sponsor = $order->sponsorname;
        @endphp
        <table style="width:100%">
            <tbody>
                <tr>
                    <td>
                        <p style="margin: 0px;">{{$sponsor->full_name}}</p>
                        <p style="margin: 0px"> {{$sponsor->address}}</p>
                        <p style="margin: 0px">{{$sponsor->no_hp}}</p>
                        <p style="margin: 0px">{{$sponsor->email}}</p>
                    </td>
                    <td class="text-right">
                        <table style="width: 100%" class="text-right">
                            <tbody>
                                <tr>
                                    <td class="text-right">
                                        Order Number :
                                    </td>
                                    <td>
                                        {{$order->order_id}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">
                                        Order Date :
                                    </td>
                                    <td>
                                        {{Carbon\Carbon::parse($order->created_at)->format('F d, Y')}} 
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">
                                        Payment Method : 
                                    </td>
                                    <td>
                                        {{$order->payment_type}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%;margin-top:32px;border-collapse: collapse;" class="table">
            <thead>
                <tr style="background-color:black;color:white">
                    <th class="text-left">Detail</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderDetails as $orderDetail)
                    <tr>
                        <td style="border-top: 1px solid #dee2e6;" class="text-left">{{$orderDetail->childname->full_name}} - {{$orderDetail->monthly_subscription}} Bulan</td>
                        <td style="border-top: 1px solid #dee2e6;" class="text-right">1</td>
                        <td style="border-top: 1px solid #dee2e6;" class="text-right">Rp {{number_format($orderDetail->price, 2, ',', '.')}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td>
                        
                    </td>
                    <td class="text-right text-bold" style="border-top: 1px solid #dee2e6;">
                        Subtotal
                    </td>
                    <td class="text-right" style="border-top: 1px solid #dee2e6;">
                        Rp {{number_format($order->total_price, 2, ',', '.')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        
                    </td>
                    <td class="text-right text-bold" style="border-top: 2px solid black;border-bottom: 2px solid black;">
                        Total
                    </td>
                    <td class="text-right text-bold" style="border-top: 2px solid black;border-bottom: 2px solid black;">
                        Rp {{number_format($order->total_price, 2, ',', '.')}}
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>