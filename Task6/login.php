<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <h2>Login Form</h2>
    <form action="auth.php" method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        Remember me: <input type="checkbox" name="remember_me"><br>
        <input type="submit" name="login" value="Login">
    </form>
</body>

</html>