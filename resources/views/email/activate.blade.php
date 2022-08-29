<!DOCTYPE html>
<html>
<head>
 <title>Activation Message</title>
</head>
<body>
 
 <h1>Activation Message</h1>
 <h4>Dear {{ $mailData['username'] }},</h4>
    <p>We have accepted your registration. Your account is:</p>
    <p> Id User: {{ $mailData['id_user'] }}</p>
    <p> Username: {{ $mailData['username'] }}</p>
    <p>Click <a href="{{$mailData['link']}}">here</a> to activate your account</p>
    <h5>Thank you</h5>     
</body>
</html> 