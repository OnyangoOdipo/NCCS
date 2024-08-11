<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Username already taken!";
        } else {
            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            if ($stmt->execute()) {
                // Set session variables and redirect to dashboard.php
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['username'] = $username;
                $_SESSION['user_role'] = $role;
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="../css/authentication.css">
    </head>
<body>
<body>
    <header>
        <h1></h1>
    </header>
    <div class="container">
        <h2>Create a New Account</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            Username <input type="text" name="username" required><br>
            Password <input type="password" name="password" required><br>
            Confirm Password <input type="password" name="confirm_password" required><br>
            Role 
            <select name="role" required>
                <option value="supervisor">Supervisor</option>
                <option value="intern">Intern</option>
            </select><br>
            <input type="submit" name="register" value="Register">
        </form>
        <br>
        <a href="dashboard.php">Back to Login</a>
    </div>
</body>
</html>