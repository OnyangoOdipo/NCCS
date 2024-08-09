<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Fetch user data from the database based on the selected role
    $stmt = $conn->prepare("SELECT id, role, password FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_role'] = $row['role'];

            // Redirect based on user role
            if ($row['role'] == 'supervisor') {
                header('Location: admin_dashboard.php');
            } elseif ($row['role'] == 'intern') {
                header('Location: intern_dashboard.php');
            }
            exit;
        } else {
            $error = "Invalid username, password, or role!";
        }
    } else {
        $error = "Invalid username, password, or role!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Login</title>
    <link rel="stylesheet" href="/css/newadmin_dashboard.css">
</head>
<body>
    <header>
        <h1>Welcome to the Task Management System Dashboard</h1>
    </header>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="dashboard.php">
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            Role: 
            <select name="role" required>
                <option value="supervisor">Supervisor</option>
                <option value="intern">Intern</option>
            </select><br>
            <input type="submit" name="login" value="Login">
        </form>
        <br>
        <a href="register.php">Don't have an account? Register</a>
    </div>
</body>
</html>
