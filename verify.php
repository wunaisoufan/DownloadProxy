<?php
session_start();

$correct_password = 'sfwww.cn'; // 替换为你的密码

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_password = $_POST['password'];

    if ($input_password === $correct_password) {
        $_SESSION['verified'] = true;
        header("Location: protected.php");
        exit();
    } else {
        echo "<p style='color:red;'>密码错误，请重试！</p>";
        header("Refresh: 3; url=index.php");
        exit();
    }
}
?>