<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user has answered 3 questions
$query = "SELECT COUNT(*) AS count FROM user_questions WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$questions_needed = $row['count'] < 3;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EDU HUB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            padding: 20px;
        }
        .profile-icon {
            display: block;
            margin: 20px auto;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
        }
        .main-content {
            margin-left: 270px;
            padding: 20px;
        }
        .question-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            margin: auto;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <img src="profile.png" class="profile-icon" alt="Profile">
    <h4 class="text-center">Welcome, User</h4>
</div>

<div class="main-content">
    <h2>Dashboard</h2>

    <?php if ($questions_needed): ?>
        <div class="question-box">
            <h4 id="question-text">Loading question...</h4>
            <input type="text" id="answer" class="form-control mt-2" placeholder="Your answer">
            <button class="btn btn-primary mt-2" onclick="submitAnswer()">Submit</button>
        </div>
    <?php endif; ?>
</div>

<script>
let questions = ["What is your favorite subject?", "What are your learning goals?", "How do you prefer to study?"];
let currentQuestion = 0;

function submitAnswer() {
    let answer = document.getElementById("answer").value;
    if (!answer) return alert("Please enter an answer.");

    fetch("save_answer.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ question: questions[currentQuestion], answer: answer })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentQuestion++;
            if (currentQuestion < questions.length) {
                document.getElementById("question-text").innerText = questions[currentQuestion];
                document.getElementById("answer").value = "";
            } else {
                location.reload(); // Reload after answering all questions
            }
        } else {
            alert("Error saving answer.");
        }
    });
}

// Load first question
document.getElementById("question-text").innerText = questions[currentQuestion];
</script>

</body>
</html>
