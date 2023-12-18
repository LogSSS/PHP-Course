<?php
session_start();

include 'sql.php';

$sql = new SQL();
$conn = $sql->getConnect();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    $conn->exec($query);
    header("Location: index.php");
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $remember_me = isset($_POST["remember_me"]);

    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row["password"])) {
        $_SESSION["user_id"] = $row["id"];
        $_SESSION["role"] = $row["role"];

        if ($remember_me) {
            setcookie("user_id", $row["id"], time() + 3600 * 24 * 30);
            setcookie("role", $row["role"], time() + 3600 * 24 * 30);
        } else {
            setcookie("user_id", "", time() - 3600);
            setcookie("role", "", time() - 3600);
        }

        header("Location: index.php");
    }
}

if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
    setcookie("user_id", "", time() - 3600);
    setcookie("role", "", time() - 3600);
    header("Location: index.php");
}
