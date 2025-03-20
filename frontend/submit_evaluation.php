<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted and endDate is provided
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['endDate'])) {
    $endDate = $_GET['endDate'];
    $systemDate = date("Y-m-d");

    // Validate end date
    if ($endDate === $systemDate) {
        // Fetch correct answers from the database
        $query = "SELECT `id`, `answer` FROM `questions`";
        $result = $conn->query($query);

        $score = 0;
        $total = $result->num_rows;

        // Compare user answers with correct answers
        while ($row = $result->fetch_assoc()) {
            $question_id = $row['id'];
            $correct_answer = $row['answer'];

            if (isset($_POST["answer_$question_id"]) && $_POST["answer_$question_id"] === $correct_answer) {
                $score++;
            }
        }

        // Calculate score percentage
        $percentage = ($total > 0) ? ($score / $total) * 100 : 0;

        // Check if user is logged in
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $username = $_SESSION['username'];

            // Insert score into the database using prepared statement
            $stmt = $conn->prepare("INSERT INTO quiz_scores (user_id, score) VALUES (?, ?)");
            $stmt->bind_param("id", $user_id, $percentage);
            $stmt->execute();
            $stmt->close();
        } else {
            $username = "Guest";
        }

        // Store score and username in session for certificate
        $_SESSION['score'] = $percentage;
        $_SESSION['certificate_name'] = $username;

        // Redirect to certificate.html
        header("Location: ../Frontend/certificate.html");
        exit();
    } else {
        echo "<h1>Access Denied</h1>";
        echo "<p>The date does not match today's system date. Please try again.</p>";
    }
} else {
    echo "<h1>Invalid Access</h1>";
    echo "<p>Please submit the form properly.</p>";
}
?>
