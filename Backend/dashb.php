<?php
include '../db.php'; // Ensure correct path

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow frontend access

try {
    $conn = new PDO("mysql:host=localhost;dbname=studyhub", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch subjects
    $subjectsQuery = $conn->query("SELECT name FROM subjects");
    $subjects = $subjectsQuery->fetchAll(PDO::FETCH_COLUMN);

    // Fetch tasks
    $tasksQuery = $conn->query("SELECT title FROM tasks");
    $tasks = $tasksQuery->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        "subjects" => $subjects,
        "tasks" => $tasks
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database connection failed!"]);
}
?>
