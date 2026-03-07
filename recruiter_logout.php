<?php
session_start();

// Clear only recruiter session keys
unset($_SESSION['recruiter_id']);
unset($_SESSION['recruiter_name']);

// Optionally destroy the whole session if you don't want to keep developer session
// session_destroy();  ← uncomment only if you want full logout for both roles

header("Location: recruiter_login.php");
exit();
?>