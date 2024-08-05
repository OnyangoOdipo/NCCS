<?php
include '../includes/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <h1>Welcome to the Dashboard</h1>
    <?php
    echo "<p>Logged in as: " . $_SESSION['role'] . "</p>";
    ?>
    <a href="logout.php">Logout</a>
</body>
</html>
