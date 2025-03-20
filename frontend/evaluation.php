<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the date is provided
if (isset($_GET['endDate'])) {
    $endDate = $_GET['endDate'];
    $systemDate = date("Y-m-d");

    // Compare end date with system date
    if ($endDate === $systemDate) {
        // Fetch questions from the database
        $query = "SELECT `id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `answer` FROM `questions`";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            echo "<h1>Evaluation Questions</h1>";
            echo "<form action='../frontend/submit_evaluation.php?endDate=$endDate' method='POST'>";

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div style='margin-bottom: 20px;'>";
                echo "<p><strong>Q: {$row['question']}</strong></p>";
                echo "<input type='radio' name='answer_{$row['id']}' value='A' required> {$row['option_a']}<br>";
                echo "<input type='radio' name='answer_{$row['id']}' value='B'> {$row['option_b']}<br>";
                echo "<input type='radio' name='answer_{$row['id']}' value='C'> {$row['option_c']}<br>";
                echo "<input type='radio' name='answer_{$row['id']}' value='D'> {$row['option_d']}<br>";
                echo "</div>";
            }

            echo "<button type='submit'>Submit</button>";
            echo "</form>";
        } else {
            echo "<h1>No questions available</h1>";
        }
    } else {
        echo "<h1>Access Denied</h1>";
        echo "<p>The date does not match today's system date. Please try again.</p>";
    }
} else {
    echo "<h1>Invalid Access</h1>";
    echo "<p>No date provided. Please go back and enter the date.</p>";
}
?>
