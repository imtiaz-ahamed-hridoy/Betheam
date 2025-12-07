<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forget Password OTP</title>
</head>
<body>

    <h1>Forget Password OTP</h1>

    <p> 
        Name: {{ $user->name ? $user->name : 'N/A'}} <br>
        Email: {{ $user->email }} <br>
        OTP: {{ $user->otp_code }}

    </p>
    
</body>
</html>