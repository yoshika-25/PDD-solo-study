<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

echo "Welcome, " . $_SESSION["email"] . "! <a href='logout.php'>Logout</a>";
?>
