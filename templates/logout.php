<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE user_logs SET logout_time = NOW() WHERE user_id = ? ORDER BY login_time DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

session_destroy();
header("Location: login.php");
exit;
?>
