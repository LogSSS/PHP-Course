<?php
session_start();

error_reporting(E_ERROR | E_PARSE);

include 'auth.php';

if (isset($_SESSION["user_id"])) {
    echo "<h1>Welcome, " . $_SESSION["role"] . "!</h1>";
    echo "<a href='auth.php?logout=true'>Logout</a>";
} else {
    echo "<h1>Welcome, Guest!</h1>";
    echo "<a href='login.php'>Login</a> | <a href='reg.php'>Register</a>";
}
