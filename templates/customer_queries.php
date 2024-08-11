<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $issue = $_POST['issue'];

    $stmt = $conn->prepare("INSERT INTO customer_queries (user_id, issue) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $issue);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Query submitted successfully</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Retrieve the issues from the database
$query = "SELECT u.username, cq.issue FROM customer_queries cq 
          JOIN users u ON cq.user_id = u.id 
          ORDER BY cq.id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Queries</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        header {
            background-color: #4a7b1c;
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-top: 0;
        }
        form {
            margin-bottom: 20px;
        }
        form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        form input[type="submit"] {
            background-color: #4a7b1c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #367312;
        }
        .query-list {
            margin-top: 20px;
        }
        .query-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .query-item:last-child {
            border-bottom: none;
        }
        .query-item h3 {
            margin: 0;
            font-size: 18px;
            color: #4a7b1c;
        }
        .query-item p {
            margin: 5px 0 0;
        }
        .query-item span {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <header>
        <h1>Customer Queries</h1>
    </header>
    <div class="container">

        <div class="query-list">
            <h2>Submitted Queries</h2>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="query-item">
                        <h3><?php echo htmlspecialchars($row['username']); ?></h3>
                        <p><?php echo htmlspecialchars($row['issue']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No queries submitted yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
