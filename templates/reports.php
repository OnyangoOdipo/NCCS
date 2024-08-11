<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Calculate total time spent by all users from login to logout
$query = "SELECT u.username, ul.login_time, ul.logout_time FROM user_logs ul
          JOIN users u ON ul.user_id = u.id
          ORDER BY u.username, ul.login_time ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$users_times = [];

while ($row = $result->fetch_assoc()) {
    $login_time = new DateTime($row['login_time']);
    $logout_time = new DateTime($row['logout_time']);
    $interval = $login_time->diff($logout_time);
    $time_spent = $interval->h * 3600 + $interval->i * 60 + $interval->s; // Total time in seconds

    if (!isset($users_times[$row['username']])) {
        $users_times[$row['username']] = 0;
    }
    $users_times[$row['username']] += $time_spent;
}

$stmt->close();

$total_hours = [];
$total_minutes = [];
$total_seconds = [];

foreach ($users_times as $user => $total_time_spent) {
    $total_hours[$user] = floor($total_time_spent / 3600);
    $total_minutes[$user] = floor(($total_time_spent % 3600) / 60);
    $total_seconds[$user] = $total_time_spent % 60;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4a7b1c;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-top: 0;
            font-size: 24px;
            color: #4a7b1c;
        }
        .time-spent {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        .user-time {
            margin: 20px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .user-time h2 {
            font-size: 18px;
            margin: 0;
            color: #4a7b1c;
        }
        .user-time p {
            font-size: 16px;
            margin: 5px 0 0 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>User Activity Report</h1>
    </header>
    <div class="container">
        <div class="time-spent">
            Total Time Spent on the System: <?php echo sprintf('%02d:%02d:%02d', array_sum($total_hours), array_sum($total_minutes), array_sum($total_seconds)); ?>
        </div>

        <?php foreach ($users_times as $user => $time): ?>
            <div class="user-time">
                <h2><?php echo htmlspecialchars($user); ?></h2>
                <p>Total Time: <?php echo sprintf('%02d:%02d:%02d', $total_hours[$user], $total_minutes[$user], $total_seconds[$user]); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
