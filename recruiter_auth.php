<?php
session_start();
if (!isset($_SESSION["recruiter_id"])) {
    header("Location: recruiter_login.php");
    exit();
}
?>