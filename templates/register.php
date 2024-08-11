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
                // Set session variables
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                // Record the registration time in user_logs
                $login_time = date('Y-m-d H:i:s');
                $user_id = $stmt->insert_id;
                $stmt = $conn->prepare("INSERT INTO user_logs (user_id, login_time) VALUES (?, ?)");
                $stmt->bind_param("is", $user_id, $login_time);
                $stmt->execute();
                $stmt->close();

                // Redirect based on user role
                if ($role === 'supervisor') {
                    header('Location: admin_dashboard.php');
                } elseif ($role === 'intern') {
                    header('Location: intern_dashboard.php');
                } else {
                    // Default case: if the role is something else, redirect to a general dashboard
                    header('Location: ../index.php');
                }
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="../css/authentication.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #4a7b1c;
        }
input[type="text"], input[type="password"], select {
width: 100%;
padding: 10px;
margin-bottom: 15px;
border-radius: 5px;
border: 1px solid #ddd;
}
input[type="submit"] {
background-color: #4a7b1c;
color: white;
padding: 10px;
border: none;
border-radius: 5px;
cursor: pointer;
width: 100%;
}
input[type="submit"]
{
background-color: #367312;
}
a {
color: #4a7b1c;
text-decoration: none;
}
.error {
color: red;
margin-bottom: 10px;
}
</style>

</head>
<body>
    <div class="container">
        <h2>Create a New Account</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="supervisor">Supervisor</option>
            <option value="intern">Intern</option>
        </select>

        <input type="submit" name="register" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
       
