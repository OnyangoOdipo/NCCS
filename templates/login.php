<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password, $role);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($password, $hashed_password)) {
        session_start();
        $_SESSION['user_id'] = $id;
        $_SESSION['role'] = $role;

        header('Location: dashboard.php');
    } else {
        echo "Invalid credentials";
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
</head>
<body>
    <form id="loginForm" method="POST" action="login.php">
        <div class="auth-container">
            <h1>Login to NEMA</h1>
            <form action="/login" method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login</button>
            </form>
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
                element: '#loginForm button[type="submit"]',
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
                // Clear the tutorial state from local storage when the tutorial finishes
                localStorage.removeItem('tutorialSteps');
                localStorage.removeItem('currentStep');
            }).start();
        }
    </script>
</body>
</html>
