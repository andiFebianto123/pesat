<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Boostrap 5</title>
    <link rel="stylesheet" href={{ asset('css/bootstrap.min.css') }}>
</head>
<body>
    <div class="container">
    
    <div class="bd-example">
    <img src="{{URL::asset('storage/image_content/child.jpg')}}" class="rounded float-start">
    </div>
        <div class="bd-callout bd-callout-info">
        <strong>
            Donation
        </strong>
        </div>    
    </div>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
</body>
</html>