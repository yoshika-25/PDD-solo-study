<?php
session_start();
header("Content-Type: application/json");
require_once "db.php"; // Database connection

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["email"]) || empty($data["password"])) {
    echo json_encode(["status" => 400, "error" => "Missing email or password"]);
    exit;
}

// Sanitize input
$email = filter_var(trim($data["email"]), FILTER_SANITIZE_EMAIL);
$password = trim($data["password"]);

// Fetch user from database
try {
    $stmt = $conn->prepare("SELECT id, username, email, password FROM signup WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password
    if ($user && password_verify($password, $user["password"])) {
        // Set session
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["email"] = $user["email"];

        echo json_encode(["status" => 200, "message" => "Login successful", "username" => $user["username"]]);
    } else {
        echo json_encode(["status" => 401, "error" => "Invalid email or password"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => 500, "error" => "Database error: " . $e->getMessage()]);
}
?>
