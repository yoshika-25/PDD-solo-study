<?php
include 'db.php';


header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username'], $data['email'], $data['password'])) {
    $username = $data['username'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash password

    try {
        $stmt = $conn->prepare("INSERT INTO signup (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password]);
        
        echo json_encode(["message" => "Registration successful", "status" => 200]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "User already exists or invalid data", "status" => 400]);
    }
} else {
    echo json_encode(["error" => "Invalid input", "status" => 400]);
}
?>
