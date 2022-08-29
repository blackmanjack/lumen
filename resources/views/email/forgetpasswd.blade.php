<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate Account</title>
</head>
<body>
    <h3>Dear {{ $mailData['username'] }}, </h3>
    <p>We have accepted your forget password request. Use this password for log in.</p>
    <p><h4>{{ $mailData['passwd'] }}</h4></p>
    <p>Thank You</p>
</body>
</html>