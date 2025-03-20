<?php
session_start();
header("Content-Type: application/json");
require_once "../Backend/db.php"; // Database connection

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => 401, "error" => "User not logged in"]);
    exit;
}

try {
    $user_id = $_SESSION["user_id"];
    $stmt = $conn->prepare("SELECT username FROM signup WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $score_stmt = $conn->prepare("SELECT score FROM quiz_scores WHERE user_id = ?");
    $score_stmt->execute([$user_id]);
    $score = $score_stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => 200,
        "name" => $user["username"] ?? "John Doe",
        "score" => $score["score"] ?? "0",
        "date" => date("F d, Y"), // Current date
        "certificate_id" => "PYT-" . date("Ymd") . "-" . str_pad($user_id, 4, "0", STR_PAD_LEFT)
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => 500, "error" => $e->getMessage()]);
}
?>
