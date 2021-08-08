<?php
/*
    Client login handler written in PHP for StaticPanel
    Version: 0.1
*/
if (!isset($_POST['username'])) {
    die("Username can't be empty!");
}
if (!isset($_POST['password'])) {
    die("Password can't be empty!");
}

$host  = $_SERVER["HTTP_HOST"];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$link = "login.html?login_fail=1";

function fail() {
    header("Location: https://$host/$link");
}

$password = sha1($_POST['password']);
$username = $_POST['username'];
require("../../config/db/config.php");
$con_string = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($con_string->connect_error) {
    die("Database connection error!");
}

$sql = "SELECT `username`, `uid` FROM `scp_users` WHERE (( `password` = " . $password . "))";
$result = $con_string->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if ($user['username'] == $username) {
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        setcookie("logged_in", "true", time() + 60 * 60, "/");
        setcookie("username", $username, time() + 60 * 60, "/");
    } else {
        fail();
    }
}