<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Confirmation</title>
</head>
<body>
    <h1>User Confirmation</h1>

    <p> Name: {{ $user->name ? $user->name : 'N/A'}} <br>
        Email: {{ $user->email }} <br>
        Phone: {{ $user->phone }} <br>
        OTP: {{ $user->otp_code }}

    </p>

</body>
</html>