<?php
session_start();

// Database Connection
$host = "localhost"; // Change if needed
$username = "root"; // Default for XAMPP/MAMP
$password = ""; // Leave empty for XAMPP
$database = "user_system"; // Ensure this is correct

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle Login Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required."]);
        exit();
    }

    $query = "SELECT * FROM `admin` WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Query preparation failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        echo json_encode(["status" => "success", "redirect" => "admindashboard.html"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Study Hub - Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-green-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-lg shadow-lg w-96 border border-green-300">
    <div class="flex justify-center mb-4">
      <img src="https://i.imgur.com/UOLOnGk.png" alt="Study Hub Logo" class="w-16 h-16">
    </div>
    <h2 class="text-center text-2xl font-bold text-green-600">Study Hub - Admin Login</h2>
    
    <form id="adminLoginForm" class="mt-4">
      <label class="block text-sm font-semibold">Email address</label>
      <input type="email" placeholder="Email" name="email" id="email" class="w-full p-2 mt-1 border rounded-md bg-gray-100" required>
      
      <label class="block mt-3 text-sm font-semibold">Password</label>
      <input type="password" placeholder="Enter your Password" name="password" id="password" class="w-full p-2 mt-1 border rounded-md bg-gray-100" required>
      
      <button type="submit" class="w-full mt-4 p-2 bg-green-900 text-white rounded-md text-lg font-semibold hover:bg-green-800">
        Login now
      </button>
      
      <div id="error-message" class="text-center text-red-500 mt-2 hidden"></div>
    </form>
  </div>

  <script>
    $(document).ready(function() {
      $("#adminLoginForm").submit(function(event) {
        event.preventDefault(); // Prevent form reload

        var email = $("#email").val().trim();
        var password = $("#password").val().trim();

        $.ajax({
          url: "adminlogin.php",  // The same file handles the request
          type: "POST",
          data: { email: email, password: password },
          dataType: "json",
          success: function(response) {
            if (response.status === "success") {
              window.location.href = response.redirect; // Redirect to admin dashboard
            } else {
              $("#error-message").text(response.message).removeClass("hidden");
            }
          },
          error: function(xhr) {
            console.log("AJAX Error:", xhr.responseText);
            $("#error-message").text("Something went wrong. Please try again.").removeClass("hidden");
          }
        });
      });
    });
  </script>
</body>
</html>
