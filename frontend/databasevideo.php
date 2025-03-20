<?php
// Previous PHP code remains same
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['add_video'])) {
    $title = $_POST['title'];
    $video_url = $_POST['video_url'];
    $time_duration = $_POST['time_duration'];
    $task_date = $_POST['task_date'];
    $sql = "INSERT INTO videos (title, video_url, Time_Duration, Task_Date) VALUES ('$title', '$video_url', '$time_duration', '$task_date')";
    $conn->query($sql);
}

if (isset($_POST['delete_video'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM videos WHERE id='$id'";
    $conn->query($sql);
}

if (isset($_POST['update_video'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $video_url = $_POST['video_url'];
    $time_duration = $_POST['time_duration'];
    $task_date = $_POST['task_date'];
    $sql = "UPDATE videos SET title='$title', video_url='$video_url', Time_Duration='$time_duration', Task_Date='$task_date' WHERE id='$id'";
    $conn->query($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Study Hub Admin - Video Management</title>
    <style>
        /* Previous styles remain same */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            padding: 2rem;
            color: white;
        }

        h2 {
            text-transform: uppercase;
            margin: 2rem 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            display: inline-block;
        }

        h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #fdbb2d, transparent);
            border-radius: 2px;
        }

        form {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: perspective(1000px) rotateX(5deg);
            transition: transform 0.3s ease;
        }

        form:hover {
            transform: perspective(1000px) rotateX(0deg);
        }

        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 0.8rem;
            margin: 0.5rem 0;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        button {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            background: linear-gradient(45deg, #fdbb2d, #b21f1f);
            color: white;
            cursor: pointer;
            margin: 0.5rem 0.2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        ul {
            list-style: none;
        }
    </style>
</head>
<body>
    <h2>Upload Video</h2>
    <form method="POST">
        <input type="text" name="title" placeholder="Video Title" required>
        <input type="text" name="video_url" placeholder="Video URL" required>
        <input type="number" name="time_duration" placeholder="Time Duration (in minutes)" required>
        <input type="date" name="task_date" required>
        <button type="submit" name="add_video">Add Video</button>
    </form>

    <h2>Manage Videos</h2>
    <ul>
        <?php
        $result = $conn->query("SELECT * FROM videos");
        while ($row = $result->fetch_assoc()) {
            echo "<li>
                <div><strong>{$row['title']}</strong></div>
                <form method='POST'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    <input type='text' name='title' value='{$row['title']}' required>
                    <input type='text' name='video_url' value='{$row['video_url']}' required>
                    <input type='number' name='time_duration' value='{$row['Time_Duration']}' required>
                    <input type='date' name='task_date' value='{$row['Task_Date']}' required>
                    <button type='submit' name='update_video'>Update</button>
                    <button type='submit' name='delete_video'>Delete</button>
                </form>
            </li>";
        }
        ?>
    </ul>
</body>
</html>
