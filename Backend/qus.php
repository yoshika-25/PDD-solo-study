<?php
// Database connection
$servername = "localhost";
$username = "root";  // Change if needed
$password = "";      // Change if needed
$database = "user_system"; // Your database name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch questions from the 'questions' table
$query = "SELECT * FROM questions ORDER BY id ASC";
$result = mysqli_query($conn, $query);

$questions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $questions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Python Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .quiz-container {
            max-width: 800px;
            margin: 50px auto;
            background: #ffffff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            border-left: 6px solid #007bff;
        }
        .option {
            width: 100%;
            text-align: left;
        }
        .btn-option {
            width: 100%;
            text-align: left;
            padding: 10px;
            margin-top: 10px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }
        .btn-option:hover {
            background-color: #0056b3;
        }
        #next-btn {
            display: none;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
        }
        #feedback {
            margin-top: 10px;
            font-weight: bold;
            font-size: 18px;
        }
        .question-number {
            font-weight: bold;
            font-size: 18px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="quiz-container p-4">
        <h2 class="text-center mb-4">Python Quiz</h2>
        <div id="question-container">
            <p class="question-number">Question <span id="question-number"></span>:</p>
            <h5 id="question-text" class="mb-3"></h5>
            <div class="options">
                <button class="btn btn-option" onclick="checkAnswer('A')"></button>
                <button class="btn btn-option" onclick="checkAnswer('B')"></button>
                <button class="btn btn-option" onclick="checkAnswer('C')"></button>
                <button class="btn btn-option" onclick="checkAnswer('D')"></button>
            </div>
            <p id="feedback" class="text-center mt-3"></p>
            <button id="next-btn" class="btn w-100" onclick="nextQuestion()">Next</button>
        </div>
    </div>

    <script>
        const questions = <?php echo json_encode($questions); ?>;
        let currentQuestionIndex = 0;
        let score = 0;

        function loadQuestion() {
            if (currentQuestionIndex >= questions.length) {
                document.getElementById("question-container").innerHTML = `
                    <h3>Quiz Completed! 🎉</h3>
                    <p>Your Score: <strong>${score}/${questions.length}</strong></p>`;
                return;
            }

            let question = questions[currentQuestionIndex];
            document.getElementById("question-number").innerText = currentQuestionIndex + 1;
            document.getElementById("question-text").innerText = question.question;
            let options = document.getElementsByClassName("btn-option");
            options[0].innerText = "A) " + question.option_a;
            options[1].innerText = "B) " + question.option_b;
            options[2].innerText = "C) " + question.option_c;
            options[3].innerText = "D) " + question.option_d;

            document.getElementById("next-btn").style.display = "none";
            document.getElementById("feedback").innerText = "";
        }

        function checkAnswer(selected) {
            let correct = questions[currentQuestionIndex].correct_option;
            let feedback = document.getElementById("feedback");

            if (selected === correct) {
                feedback.innerText = "✅ Correct!";
                feedback.style.color = "green";
                score++;
            } else {
                feedback.innerText = `❌ Wrong (Correct: ${correct})`;
                feedback.style.color = "red";
            }
            document.getElementById("next-btn").style.display = "block";
        }

        function nextQuestion() {
            currentQuestionIndex++;
            loadQuestion();
        }

        loadQuestion();
    </script>
</body>
</html>
