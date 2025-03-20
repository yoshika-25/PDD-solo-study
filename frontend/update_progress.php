<?php
session_start(); // Start the session
include '../Backend/db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ensure the user is logged in and has a session user_id
    if (!isset($_SESSION['user_id'])) {
        echo "error: User not logged in.";
        exit();
    }

    $user_id = $_SESSION['user_id']; // Get the logged-in user ID
    $timestamp = $_POST['timestamp']; // Get the timestamp from the POST request

    // Validate the timestamp (optional, depending on your requirements)
    if (empty($timestamp)) {
        echo "error: Timestamp is required.";
        exit();
    }

    // Check if the record exists
    $query = "SELECT * FROM video_progress WHERE Signup_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Update existing record
        $updateQuery = "UPDATE video_progress SET timestamp = :timestamp WHERE Signup_id = :user_id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR); // Bind as string
        $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $updateStmt->execute();
    } else {
        // Insert a new record
        $insertQuery = "INSERT INTO video_progress (Signup_id, timestamp) VALUES (:user_id, :timestamp)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insertStmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR); // Bind as string
        $insertStmt->execute();
    }

    echo "success";
}
?>