<?php
// Start the session
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all subjects for the dropdown
$query = "SELECT id, title FROM subjects";
$result = $conn->query($query);

$subjects = array();
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

// Fetch subject details if an ID is passed
$subjectDetails = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT title, subtitle, category FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subjectDetails = $result->fetch_assoc();
    
    // Store the selected subject title in the session
    $_SESSION['selected_subject'] = $subjectDetails['title'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Subject</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background: linear-gradient(45deg, #ff3366, #ba265d, #0033cc, #003366);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            min-height: 100vh;
            color: #2c3e50;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 
                0 20px 25px -5px rgba(0, 0, 0, 0.3),
                0 10px 10px -5px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .btn-success {
            background: linear-gradient(45deg, #ff3366, #0033cc);
            border: none;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            background: linear-gradient(45deg, #0033cc, #ff3366);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Select Subject</h2>

            <!-- Dropdown for Selecting Subject -->
            <div class="mb-3">
                <label for="subjectDropdown" class="form-label">Select Subject:</label>
                <select id="subjectDropdown" class="form-select">
                    <option value="">-- Select Subject --</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>" <?= isset($_GET['id']) && $_GET['id'] == $subject['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($subject['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Form Fields -->
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" class="form-control" placeholder="Enter Subject Name" value="<?= htmlspecialchars($subjectDetails['title'] ?? '') ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="subtitle" class="form-label">Subtitle</label>
                <textarea id="subtitle" class="form-control" rows="3" placeholder="Enter Subtitles" readonly><?= htmlspecialchars($subjectDetails['subtitle'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <textarea id="category" class="form-control" rows="3" placeholder="Enter Categories" readonly><?= htmlspecialchars($subjectDetails['category'] ?? '') ?></textarea>
            </div>

            <!-- Add Task Button -->
            <button id="addTask" class="btn btn-success w-100">Add Task</button>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Change event for subject dropdown
            $("#subjectDropdown").change(function () {
                let subjectId = $(this).val();
                if (subjectId) {
                    window.location.href = "subjectuser.php?id=" + subjectId;
                } else {
                    $("#title, #subtitle, #category").val("");
                }
            });

            // Redirect to calculate.html when Add Task is clicked
            $("#addTask").click(function () {
                let selectedSubject = $("#subjectDropdown").val();
                if (selectedSubject) {
                    window.location.href = "calculate.html";  
                } else {
                    alert("Please select a subject before adding a task.");
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>