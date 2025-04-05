{{-- resources/views/emails/forget-password.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <p>Click the following link to reset your password:</p>
    <a href="{{ $resetUrl }}">Reset Password</a>
</body>
</html>
