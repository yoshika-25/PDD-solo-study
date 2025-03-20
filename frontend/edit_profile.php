<?php
session_start();
require_once("../Backend/db.php"); // Database connection

// Redirect to login if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];
$email = $_SESSION["email"];

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_username = trim($_POST["username"]);
    $new_email = trim($_POST["email"]);
    $new_password = !empty($_POST["password"]) ? password_hash($_POST["password"], PASSWORD_BCRYPT) : null;

    try {
        if (!empty($new_username) && !empty($new_email)) {
            // Update query
            if ($new_password) {
                $stmt = $conn->prepare("UPDATE signup SET username = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$new_username, $new_email, $new_password, $user_id]);
            } else {
                $stmt = $conn->prepare("UPDATE signup SET username = ?, email = ? WHERE id = ?");
                $stmt->execute([$new_username, $new_email, $user_id]);
            }

            // Update session data
            $_SESSION["username"] = $new_username;
            $_SESSION["email"] = $new_email;

            $message = "Profile updated successfully!";
        } else {
            $message = "Username and email cannot be empty.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}
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
            max-width: 500px;
            margin: 50px auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            padding: 0.8rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            text-align: center;
            margin-top: 1rem;
            font-size: 1rem;
            color: green;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>

        <?php if (!empty($message)) : ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="edit_profile.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="password">New Password (Leave blank to keep current password)</label>
            <input type="password" id="password" name="password">

            <button type="submit">Update Profile</button>
        </form>

        <a href="profile.php" class="back-link">Back to Profile</a>
    </div>
</body>
</html>
