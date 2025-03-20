<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$username = $_SESSION["username"];
$email = $_SESSION["email"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 3px solid #007bff;
        }

        .profile-name {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .profile-title {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
        }

        .section-title {
            color: #333;
            margin-bottom: 1rem;
            border-bottom: 2px solid #eee;
            padding-bottom: 0.5rem;
        }

        .edit-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #007bff;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .edit-button:hover {
            background: #0056b3;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1rem;
        }

        .contact-link {
            color: #007bff;
            text-decoration: none;
        }

        .contact-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="profile-header">
            <img src="https://toppng.com/uploads/preview/file-svg-profile-icon-vector-11562942678pprjdh47a8.png" alt="Profile Picture" class="profile-image">
            <h1 class="profile-name"><?php echo htmlspecialchars($username); ?></h1>
            <p class="profile-title">Web Developer</p>
        </header>

        <section class="section">
            <h2 class="section-title">About Me</h2>
            <p>
                Welcome, <?php echo htmlspecialchars($username); ?>! Here you can edit your profile information.
            </p>
            <button class="edit-button" onclick="window.location.href='edit_profile.php'">Edit</button>
        </section>

        <section class="section">
            <h2 class="section-title">Contact Information</h2>
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
            <div class="contact-info">
                <a href="mailto:<?php echo htmlspecialchars($email); ?>" class="contact-link">Email</a>
                <a href="https://linkedin.com/in/<?php echo htmlspecialchars($username); ?>" class="contact-link">LinkedIn</a>
                <a href="https://github.com/<?php echo htmlspecialchars($username); ?>" class="contact-link">GitHub</a>
            </div>
        </section>
    </div>
</body>
</html>
