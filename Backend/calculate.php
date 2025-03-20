<?php
include '../Backend/db.php'; // Ensure correct database connection

session_start();
header('Content-Type: application/json'); // Ensure JSON response

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "SELECT timestamp FROM video_progress WHERE Signup_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['remainingTime' => $row ? (int) $row['timestamp'] : 0]);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Read JSON input
        $inputJSON = file_get_contents("php://input");
        $data = json_decode($inputJSON, true);

        if (!$data) {
            echo json_encode(['error' => 'Invalid JSON input']);
            exit();
        }

        if (!isset($data['totalHours'], $data['startDate'], $data['endDate'])) {
            echo json_encode(['error' => 'Missing required fields']);
            exit();
        }

        $totalHours = (float) $data['totalHours'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];

        if (!$totalHours || !$startDate || !$endDate) {
            echo json_encode(['error' => 'Invalid input values']);
            exit();
        }

        // Calculate total study days
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days + 1;

        if ($days <= 0) {
            echo json_encode(['error' => 'End date must be after start date']);
            exit();
        }

        // Calculate daily study time in minutes
        $dailyMinutes = ($totalHours * 60) / $days;

        echo json_encode(['dailyMinutes' => round($dailyMinutes, 2)]);
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
    exit();
}

// If request method is invalid
echo json_encode(['error' => 'Invalid request method']);
exit();
?>
