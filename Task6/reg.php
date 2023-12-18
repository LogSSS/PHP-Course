<!DOCTYPE html>
<html>

<head>
    <title>Registration</title>
</head>

<body>
    <h2>Registration Form</h2>
    <form action="auth.php" method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        Role: <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br>
        <input type="submit" name="register" value="Register">
    </form>
</body>

</html>