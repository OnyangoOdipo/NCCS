<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the statement to select user information based on the username
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password, $role);
    $stmt->fetch();
    $stmt->close();

    // Verify the password
    if (password_verify($password, $hashed_password)) {
        session_start();
        $_SESSION['user_id'] = $id;
        $_SESSION['role'] = $role;

        // Record the login time in user_logs
        $login_time = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("INSERT INTO user_logs (user_id, login_time) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $login_time);
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
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../css/authentication.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js/minified/introjs.min.css">
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
        .auth-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #4a7b1c;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button[type="submit"] {
            background-color: #4a7b1c;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button[type="submit"]:hover {
            background-color: #367312;
        }
        a {
            color: #4a7b1c;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <form id="loginForm" method="POST" action="login.php">
        <div class="auth-container">
            <h1>Login to NEMA</h1>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
            <p>Don't have an account? <a href="register.php">Sign up</a></p>
        </div>
    </form>

    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>

    <script>
        const tutorialSteps = JSON.parse(localStorage.getItem('tutorialSteps')) || [];
        const loginSteps = [
            {
                intro: "Enter your username here.",
                element: '#username',
                position: 'right'
            },
            {
                intro: "Enter your password here.",
                element: '#password',
                position: 'right'
            },
            {
                intro: "Finally, click the 'Login' button to log in to your account.",
                element: '#loginForm button[type=\"submit\"]',
                position: 'right'
            }
        ];
        const completeSteps = tutorialSteps.concat(loginSteps);

        if (completeSteps.length > 0) {
            introJs().setOptions({
                steps: completeSteps,
                nextLabel: 'Next',
                prevLabel: 'Previous',
                skipLabel: 'Skip',
                doneLabel: 'Finish',
                initialStep: parseInt(localStorage.getItem('currentStep'), 10) || 0
            }).oncomplete(function() {
                localStorage.removeItem('tutorialSteps');
                localStorage.removeItem('currentStep');
            }).start();
        }
    </script>
</body>
</html>
