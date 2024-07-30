<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Siostore Checkout - {{$orderId}}</title>
</head>
<body>
    <!-- Breadcrumb End -->
    <div class="container-fluid">
        <div id="sumup-card"></div>
    </div>

    <script type="text/javascript" src="https://gateway.sumup.com/gateway/ecom/card/v2/sdk.js"></script>
    <script type="text/javascript">
        SumUpCard.mount({
            id: 'sumup-card',
            checkoutId: '{{$client_secret}}',
            onResponse: function(type, body) {
                window.location.assign('{{route("payment_complete_sumup_mobile", $client_secret)}}');
                // console.log('Type', type);
                // console.log('Body', body);
            },
        });
    </script>
</body>
</html>




