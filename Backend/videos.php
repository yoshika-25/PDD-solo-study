<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_system";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle video upload
if (isset($_POST['upload_video'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $video_name = $_FILES['video']['name'];
    $video_tmp = $_FILES['video']['tmp_name'];
    $upload_folder = "uploads/";

    // Ensure the uploads directory exists
    if (!is_dir($upload_folder)) {
        mkdir($upload_folder, 0777, true);
    }

    $target_file = $upload_folder . basename($video_name);

    // Move the uploaded file
    if (move_uploaded_file($video_tmp, $target_file)) {
        $stmt = $conn->prepare("INSERT INTO videos (title, video_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $target_file);
        if ($stmt->execute()) {
            echo "Video uploaded successfully!";
        } else {
            echo "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error uploading video.";
    }
}

// Handle video deletion
if (isset($_POST['delete_video'])) {
    $id = (int) $_POST['id'];
    $stmt = $conn->prepare("SELECT video_path FROM videos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($video_path);
    $stmt->fetch();
    $stmt->close();

    // Delete the video file from the server
    if (file_exists($video_path)) {
        unlink($video_path);
    }

    // Delete record from database
    $conn->query("DELETE FROM videos WHERE id='$id'");
    echo "Video deleted successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload and Manage Videos</title>
</head>
<body>
    <h2>Upload Video</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Video Title" required>
        <input type="file" name="video" accept="video/*" required>
        <button type="submit" name="upload_video">Upload Video</button>
    </form>

    <h2>Manage Videos</h2>
    <ul>
        <?php
        $result = $conn->query("SELECT id, title, video_path FROM videos");
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['title']} 
                  <a href='{$row['video_path']}' target='_blank'>Watch</a>
                  <form method='POST' style='display:inline;'>
                      <input type='hidden' name='id' value='{$row['id']}'>
                      <button type='submit' name='delete_video'>Delete</button>
                  </form>
                  </li>";
        }
        ?>
    </ul>
</body>
</html>
