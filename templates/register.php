<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $email, $role);
    $stmt->execute();
    $stmt->close();

    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <form method="POST" action="register.php">
        <input type="text" name="username" required placeholder="Username">
        <input type="password" name="password" required placeholder="Password">
        <input type="email" name="email" placeholder="Email">
        <select name="role">
            <option value="customer">Customer</option>
            <option value="intern">Intern</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Register</button>
    </form>
</body>
</html>
