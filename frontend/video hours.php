<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = 1; // Replace with the logged-in user ID
$query = "SELECT video_hours FROM video_progress WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_video_hours = $row ? $row['video_hours'] : 0;

echo json_encode(["totalHours" => $total_video_hours]);

$stmt->close();
$conn->close();
?>
