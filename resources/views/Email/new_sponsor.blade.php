<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>
    <h1 style="
        background-color: #2196f3;
        margin-bottom: 0px;
        color: white;
        padding: 24px;
      ">
        {{ $title }}
    </h1>
    <div style="padding: 32px">
        <p>Hi Admin, Seseorang mendaftar di website pesat</p>
        <p>Nama: {{ $sponsorname }}</p>
        <p>NB: Pesan ini hanya notifikasi bisa di abaikan</p>
        <p>Terimakasih.</p>
        <div style="text-align: center; margin-top: 24px">
            <a href="{{ url('/') }}"
                style="display: inline-block; text-decoration: none; color: #2196f3">{{ config('app.name') }}</a>
            <p style="margin: 0px"></p>
            Developed by
            <a href="https://rectmedia.com" style="display: inline-block; color: #2196f3">RECTmedia</a>
        </div>
    </div>
</body>

</html>
