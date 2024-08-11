<?php
include '../includes/db.php';

session_start(); // Start the session to access the user data

// Function to generate a tracking code
function generateTrackingCode($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $user_id = $_SESSION['user_id']; // Fetch the user ID from the session

    if (isset($_POST['license_type'])) {
        // Handle license application

        $license_type = $_POST['license_type'];

        // Generate a tracking code
        $tracking_code = generateTrackingCode();

        // Insert application into the licenses table
        $stmt = $conn->prepare("INSERT INTO licenses (user_id, license_type, tracking_code) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $license_type, $tracking_code);
        $stmt->execute();
        $license_id = $stmt->insert_id;
        $stmt->close();

        // Handle document uploads
        foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['documents']['name'][$key]);
            $file_path = "../uploads/" . $file_name; // Add a slash to separate the directory and file name
            move_uploaded_file($tmp_name, $file_path);

            $stmt = $conn->prepare("INSERT INTO documents (license_id, file_path) VALUES (?, ?)");
            $stmt->bind_param("is", $license_id, $file_path);
            $stmt->execute();
            $stmt->close();
        }

        // Display a popup with the tracking code
        echo "<script>
            alert('Application submitted successfully! Your tracking code is: $tracking_code. Please note it down.');
            setTimeout(function() {
                window.location.href = 'track_application.php';
            }, 30000); // Redirect after 30 seconds
        </script>";
    } elseif (isset($_POST['tracking_code'])) {
        // Handle tracking of application status

        $tracking_code = $_POST['tracking_code'];

        $stmt = $conn->prepare("SELECT application_status FROM licenses WHERE tracking_code = ? AND user_id = ?");
        $stmt->bind_param("si", $tracking_code, $user_id);
        $stmt->execute();
        $stmt->bind_result($status);
        $stmt->fetch();
        $stmt->close();

        if ($status) {
            echo "<script>alert('Your application status is: $status');</script>";
        } else {
            echo "<script>alert('Invalid tracking code or no such application exists.');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEMA Licensing Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* General Styles */
body {
    font-family: 'Arial', sans-serif;
    color: #333;
    margin: 0;
    padding: 0;
}

/* Navbar */
.navbar {
    background-color: #4a7b1c;
    color: white;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar ul {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
}

.navbar ul li {
    margin: 0 15px;
}

.navbar ul li a {
    color: white;
    text-decoration: none;
}

.search-bar {
    display: flex;
}

.search-bar input {
    padding: 5px;
    border: none;
    border-radius: 5px 0 0 5px;
}

.search-bar button {
    padding: 5px 10px;
    border: none;
    background-color: #4a7b1c;
    color: white;
    border-radius: 0 5px 5px 0;
    cursor: pointer;
}

/* Hero Section */
.hero {
    background-image: url('images/environment.jpg');
    background-size: cover;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    height: 400px;
    padding: 60px 20px;
}

.hero h1 {
    font-size: 48px;
    margin-bottom: 20px;
}

.hero p {
    font-size: 18px;
    margin-bottom: 40px;
}

.cta-button {
    background-color: #4a7b1c;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
    display: inline-block;
}

.cta-button:hover {
    background-color: #006400;
}

/* Information Cards */
.info-cards {
    display: flex;
    justify-content: space-around;
    padding: 40px 20px;
    background-color: #f9f9f9;
}

.card {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 30%;
    text-align: center;
}

.card h3 {
    font-size: 24px;
    margin-bottom: 20px;
}

.card p {
    font-size: 16px;
    margin-bottom: 20px;
}

.card-button {
    background-color: #4a7b1c;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
}

.card-button:hover {
    background-color: #006400;
}

@media (max-width: 768px) {
    .info-cards {
        flex-direction: column;
        align-items: center;
    }

    .card {
        width: 80%;
        margin-bottom: 20px;
    }
}

/* Steps Section */
.steps {
    padding: 40px 20px;
    background-color: white;
    text-align: center;
}

.steps h2 {
    font-size: 32px;
    margin-bottom: 20px;
}

.steps ol {
    list-style-position: inside;
    font-size: 18px;
    margin-bottom: 20px;
    text-align: left;
    margin: 0 auto;
    max-width: 600px;
}

/* Modal */
.modal-content {
    background-color: #4a7b1c;
    color: #fff;
}

.modal-header, .modal-footer {
    border-color: #4a7b1c;
}

.modal-title {
    font-weight: bold;
}

.btn-primary {
    background-color: #004d00;
    border-color: #004d00;
}

.btn-primary:hover {
    background-color: #003300;
    border-color: #003300;
}

/* FAQ Section */
.faq {
    padding: 40px 20px;
    background-color: #f9f9f9;
}

.faq h2 {
    font-size: 32px;
    margin-bottom: 20px;
}

.faq-item {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.faq-item h3 {
    font-size: 24px;
    margin-bottom: 10px;
}

/* Footer */
footer {
    background-color: #4a7b1c;
    color: white;
    padding: 20px;
    text-align: center;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
}

.footer-content p {
    margin: 10px 0;
}

.social-media img {
    width: 24px;
    margin: 0 10px;
    vertical-align: middle;
}

.footer-links a {
    color: white;
    margin: 0 10px;
    text-decoration: none;
}

.footer-links a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
    <!-- Header -->
    <header class="navbar">
        <div class="logo">
            <img src="../css/logo.png" alt="NEMA Logo">
        </div>
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#licenses">Licenses</a></li>
                <li><a href="#apply">Apply</a></li>
                <li><a href="#contact">Contact Us</a></li>
            </ul>
        </nav>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <button type="submit">Search</button>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to NEMA Licensing Portal</h1>
        <p>Your gateway to environmental management in Kenya</p>
        <a href="#apply" class="cta-button">Apply for a License</a>
    </section>

    <!-- Information Cards -->
    <section class="info-cards" id="licenses">
        <h2>Available Licenses</h2>
        <br><br>
        <div class="card">
            <h3>Waste Management License</h3>
            <p>Manage and dispose of waste in compliance with NEMA guidelines.</p>
            <a href="#apply" class="card-button">Learn More</a>
        </div>
        <div class="card">
            <h3>Pollution Control License</h3>
            <p>Control emissions and pollutants to safeguard the environment.</p>
            <a href="#apply" class="card-button">Learn More</a>
        </div>
        <!-- Add more cards as needed -->
    </section>

    <!-- Steps to Apply -->
    <section class="steps" id="apply">
        <h2>How to Apply</h2>
        <ol>
            <li>Create an account or log in to the portal.</li>
            <li>Fill out the application form for the desired license.</li>
            <li>Upload necessary documents.</li>
            <li>Submit the application and wait for approval.</li>
        </ol>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyModal" id="apply-btn">
    Start Application
</button>
        <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applyModalLabel">Apply for a License</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="applicationForm" action="licensing_portal.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="license_type" class="form-label">License Type</label>
                        <select class="form-select" id="license_type" name="license_type" required>
                            <option value="business">Business License</option>
                            <option value="environmental">Environmental License</option>
                            <!-- Add more options as needed -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="documents" class="form-label">Upload Documents</label>
                        <input type="file" class="form-control" id="documents" name="documents[]" multiple required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </form>
            </div>
        </div>
    </div>
</div>
    </section>

    <section class="status-check" id="status">
    <h2>Check Application Status</h2>
    <form id="statusForm" action="licensing_portal.php" method="POST">
        <div class="mb-3">
            <label for="tracking_code" class="form-label">Tracking Code</label>
            <input type="text" class="form-control" id="tracking_code" name="tracking_code" required>
        </div>
        <button type="submit" class="btn btn-primary">Check Status</button>
    </form>
</section>


    <!-- FAQ Section -->
    <section class="faq" id="faq">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-item">
            <h3>What is the process to renew a license?</h3>
            <p>The renewal process involves logging in to your account, selecting the license you wish to renew, and following the renewal steps.</p>
        </div>
        <div class="faq-item">
            <h3>How do I track my application status?</h3>
            <p>You can track your application status by logging into your account and navigating to the "My Applications" section.</        </div>
        <!-- Add more FAQ items as needed -->
    </section>

    <!-- Footer -->
    <footer>
    <div class="footer-content">
        <p>&copy; 2024 National Environment Management Authority (NEMA) - Kenya. All rights reserved.</p>
        <div class="social-media">
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-twitter"></i></a>
            <a href="#"><i class="bi bi-linkedin"></i></a>
        </div>
        <div class="footer-links">
            <a href="#privacy">Privacy Policy</a>
            <a href="#terms">Terms & Conditions</a>
        </div>
    </div>
</footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>