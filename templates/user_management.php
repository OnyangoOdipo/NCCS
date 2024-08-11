<?php
session_start();
include '../includes/db.php';

// Ensure the user is logged in and is a supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
    header("Location: ../login.php");
    exit;
}

// Fetch all users and categorize them
$interns = [];
$normal_users = [];

$stmt = $conn->prepare("SELECT id, username, role FROM users");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if ($row['role'] == 'intern') {
        $interns[] = $row;
    } else {
        $normal_users[] = $row;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background-color: #4a7b1c;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            margin-top: 0;
            color: #4a7b1c;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            background-color: #e8f5e9;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #4a7b1c;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <header>
        <h1>User Management</h1>
    </header>
    <div class="container">
        <h2>Interns</h2>
        <ul>
            <?php foreach ($interns as $intern): ?>
                <li><?php echo htmlspecialchars($intern['username']); ?></li>
            <?php endforeach; ?>
        </ul>

        <h2>Normal Users</h2>
        <ul>
            <?php foreach ($normal_users as $user): ?>
                <li><?php echo htmlspecialchars($user['username']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
