<?php
session_start();
include '../includes/db.php';

 //Ensure the user is logged in and is a supervisor
 if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'supervisor') {
     header("Location: ../login.php");
     exit;
 }

$user_id = $_SESSION['user_id'];

// Fetch total number of users
$total_users = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $total_users = $row['total'];
}
$stmt->close();

// Fetch pending tasks
$pending_tasks = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE completed = 0");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $pending_tasks = $row['total'];
}
$stmt->close();

// Fetch resolved tasks
$resolved_issues = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE completed = 1");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $resolved_issues = $row['total'];
}
$stmt->close();

// Task Creation and Assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_task'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $intern_id = $_POST['intern_id'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, priority, due_date, supervisor_id, intern_id, completed) VALUES (?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("ssssii", $title, $description, $priority, $due_date, $user_id, $intern_id);

    if ($stmt->execute()) {
        echo "Task created successfully";
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
    <title>Supervisor Dashboard</title>
    <link rel="stylesheet" href="../css/newadmin_dashboard.css">
</head>
<body>
    <header>
        <h1>Supervisor Dashboard</h1>
    </header>
    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="#">User Management</a>
        <a href="#">Customer Queries</a>
        <a href="#">Reports</a>
        <a href="#">Settings</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <div class="stats">
            <div>Total Users<br><span><?php echo $total_users; ?></span></div>
            <div>Pending Tasks<br><span><?php echo $pending_tasks; ?></span></div>
            <div>Resolved Issues<br><span><?php echo $resolved_issues; ?></span></div>
        </div>

        <h2>Create and Assign Task</h2>
        <form method="POST" action="admin_dashboard.php">
            <input type="hidden" name="create_task" value="1">
            Title: <input type="text" name="title" required><br>
            Description: <textarea name="description"></textarea><br>
            Priority: 
            <select name="priority">
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select><br>
            Due Date: <input type="date" name="due_date" required><br>
            Assign to Intern: 
            <select name="intern_id">
                <?php
                // Fetch interns for assignment
                $stmt = $conn->prepare("SELECT id, username FROM users WHERE role='intern'");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
                $stmt->close();
                ?>
            </select><br>
            <input type="submit" value="Create Task">
        </form>

        <h2>View Assigned Tasks</h2>
        <?php
        // Fetch tasks assigned by the supervisor
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE supervisor_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo "Title: " . htmlspecialchars($row['title']) . "<br>";
            echo "Description: " . htmlspecialchars($row['description']) . "<br>";
            echo "Priority: " . htmlspecialchars($row['priority']) . "<br>";
            echo "Due Date: " . htmlspecialchars($row['due_date']) . "<br>";
            echo "Completed: " . ($row['completed'] ? 'Yes' : 'No') . "<br><br>";
        }
        $stmt->close();
        ?>
    </div>
</body>
</html>
