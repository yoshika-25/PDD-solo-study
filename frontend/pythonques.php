<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = $_POST['table'] ?? 'questions'; // Default to Python questions

$action = $_POST['action'] ?? ''; 

if ($action == 'add') {
    $question = $_POST['question'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $answer = $_POST['answer'];

    $sql = "INSERT INTO $table (question, option_a, option_b, option_c, option_d, answer) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $question, $option_a, $option_b, $option_c, $option_d, $answer);
    $stmt->execute();
    echo "Question added successfully";
}

elseif ($action == 'update') {
    $id = $_POST['id'];
    $question = $_POST['question'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $answer = $_POST['answer'];

    $sql = "UPDATE $table SET question=?, option_a=?, option_b=?, option_c=?, option_d=?, answer=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $question, $option_a, $option_b, $option_c, $option_d, $answer, $id);
    $stmt->execute();
    echo "Question updated successfully";
}

elseif ($action == 'delete') {
    $id = $_POST['id'];

    $sql = "DELETE FROM $table WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "Question deleted successfully";
}

$sql = "SELECT id, question, option_a, option_b, option_c, option_d, answer FROM $table";
$result = $conn->query($sql);
$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
    <style>
        body {
            background: linear-gradient(45deg, #ff9a9e, #fad0c4, #ffdde1);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            width: auto;
            margin: auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        select, input, button {
            display: block;
            width: 90%;
            margin: 10px auto;
            padding: 10px;
            border-radius: 5px;
            border: none;
        }
        button {
            background: #ff7675;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background: #d63031;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
    </style>
    <script>
        function editQuestion(id, question, optionA, optionB, optionC, optionD, answer) {
            document.getElementById('id').value = id;
            document.getElementsByName('question')[0].value = question;
            document.getElementsByName('option_a')[0].value = optionA;
            document.getElementsByName('option_b')[0].value = optionB;
            document.getElementsByName('option_c')[0].value = optionC;
            document.getElementsByName('option_d')[0].value = optionD;
            document.getElementsByName('answer')[0].value = answer;
            document.getElementById('updateBtn').style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Manage Questions</h2>
        <form action="" method="POST">
            <label for="table">Select Subject:</label>
            <select name="table" id="table" onchange="this.form.submit()">
                <option value="questions" <?php if ($table == 'questions') echo 'selected'; ?>>Python</option>
                <option value="javaquestions" <?php if ($table == 'javaquestions') echo 'selected'; ?>>Java</option>
                <option value="que" <?php if ($table == 'que') echo 'selected'; ?>>C</option>
            </select>
        </form>

        <form action="" method="POST">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="table" value="<?php echo $table; ?>">
            <input type="text" name="question" placeholder="Enter Question" required>
            <input type="text" name="option_a" placeholder="Option A" required>
            <input type="text" name="option_b" placeholder="Option B" required>
            <input type="text" name="option_c" placeholder="Option C" required>
            <input type="text" name="option_d" placeholder="Option D" required>
            <input type="text" name="answer" placeholder="Correct Answer" required>
            <button type="submit" name="action" value="add">Add Question</button>
            <button type="submit" name="action" value="update" id="updateBtn" style="display:none;">Update Question</button>
        </form>

        <h3>Questions List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Option A</th>
                <th>Option B</th>
                <th>Option C</th>
                <th>Option D</th>
                <th>Answer</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($questions as $q) { ?>
                <tr>
                    <td><?php echo $q['id']; ?></td>
                    <td><?php echo $q['question']; ?></td>
                    <td><?php echo $q['option_a']; ?></td>
                    <td><?php echo $q['option_b']; ?></td>
                    <td><?php echo $q['option_c']; ?></td>
                    <td><?php echo $q['option_d']; ?></td>
                    <td><?php echo $q['answer']; ?></td>
                    <td>
                        <button onclick="editQuestion('<?php echo $q['id']; ?>', '<?php echo addslashes($q['question']); ?>', '<?php echo addslashes($q['option_a']); ?>', '<?php echo addslashes($q['option_b']); ?>', '<?php echo addslashes($q['option_c']); ?>', '<?php echo addslashes($q['option_d']); ?>', '<?php echo addslashes($q['answer']); ?>')">Edit</button>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $q['id']; ?>">
                            <input type="hidden" name="table" value="<?php echo $table; ?>">
                            <button type="submit" name="action" value="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
