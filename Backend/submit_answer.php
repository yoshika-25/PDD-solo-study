<?php
session_start();
header("Content-Type: application/json");
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Please login first"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'];
$question_id = $data['question_id'];
$answer = $data['answer'];

// Insert the answer into the database
$sql = "INSERT INTO user_answers (user_id, question_id, answer) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $user_id, $question_id, $answer);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Answer submitted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to submit answer"]);
}
?>
