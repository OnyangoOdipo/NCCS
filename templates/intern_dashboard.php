<?php
session_start();
include '../includes/db.php';

// Ensure the user is logged in and is an intern
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'intern') {
//     echo '<a href="login.php">Login</a> | <a href="register.php">Register</a>';
//     exit;
// }

$user_id = $_SESSION['user_id'];

// Daily Logs
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['log_activity'])) {
    $task_id = $_POST['task_id'];
    $progress = $_POST['progress'];
    $log_date = date('Y-m-d');

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO logs (intern_id, task_id, log_date, progress) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $task_id, $log_date, $progress);

    if ($stmt->execute()) {
        echo "Activity logged successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Mark task as completed
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_completed'])) {
    $task_id = $_POST['task_id'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE tasks SET completed = 1 WHERE id = ? AND intern_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        echo "Task marked as completed";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Dashboard</title>
    <link rel="stylesheet" href="/css/newadmin_dashboard.css">
</head>
<body>
    <header>
        <h1>Intern Dashboard</h1>
    </header>
    <nav>
        <a href="intern_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <h2>Log Daily Activity</h2>
        <form method="POST" action="intern_dashboard.php">
            <input type="hidden" name="log_activity" value="1">
            Task: 
            <select name="task_id">
                <?php
                // Fetch tasks assigned to the intern
                $stmt = $conn->prepare("SELECT id, title FROM tasks WHERE intern_id=? AND completed=0");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['title']}</option>";
                }
                $stmt->close();
                ?>
            </select><br>
            Progress: <textarea name="progress" required></textarea><br>
            <input type="submit" value="Log Activity">
        </form>

        <h2>Mark Task as Completed</h2>
        <form method="POST" action="intern_dashboard.php">
            <input type="hidden" name="mark_completed" value="1">
            Task: 
            <select name="task_id">
                <?php
                // Fetch tasks assigned to the intern
                $stmt = $conn->prepare("SELECT id, title FROM tasks WHERE intern_id=? AND completed=0");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['title']}</option>";
                }
                $stmt->close();
                ?>
            </select><br>
            <input type="submit" value="Mark as Completed">
        </form>

        <div class="recent-activities">
            <h2>Recent Activities</h2>
            <?php
            // Fetch recent activities (logs) for the intern
            $stmt = $conn->prepare("SELECT * FROM logs WHERE intern_id = ? ORDER BY log_date DESC LIMIT 25");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "Date: " . htmlspecialchars($row['log_date']) . "<br>";
                echo "Progress: " . htmlspecialchars($row['progress']) . "<br><br>";
            }
            $stmt->close();
            ?>
        </div>
    </div>
</body>
</html>
