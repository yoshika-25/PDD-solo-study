<?php
include 'db.php'; // Ensure PDO connection

// Handle Delete Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $userId = intval($_POST['delete_id']); // Ensure ID is an integer
    try {
        $sql = "DELETE FROM signup WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo json_encode(["status" => 200, "message" => "User deleted successfully"]);
        } else {
            echo json_encode(["status" => 500, "error" => "Failed to delete user"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => 500, "error" => "Database error: " . $e->getMessage()]);
    }
    exit;
}

// Fetch Users
try {
    $sql = "SELECT id, username, email FROM signup";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }
        .sidebar a {
            text-decoration: none;
            color: white;
            display: block;
            padding: 15px;
            font-size: 16px;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar .active {
            background: #007bff;
        }
        .main-content {
            margin-left: 260px; /* Adjusted for sidebar */
            padding: 20px;
            width: calc(100% - 260px);
            overflow-x: auto; /* Prevents table from going under sidebar */
        }
        .sidebar .logo {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <i class="fas fa-user-shield"></i> Admin Panel
    </div>
    <a href="../frontend/admindashboard.html" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="adminuser.php"><i class="fas fa-users"></i> User Management</a>
    <a href="../frontend/addsubject.php"><i class="fas fa-file-alt"></i> Content</a>
    <a href="../frontend/pythonques.php"><i class="fas fa-question-circle"></i> Evaluation Questions</a>
    <a href="#"><i class="fas fa-cogs"></i> Settings</a>
    <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2 class="mb-4">User Management</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTable">
                <?php if (!empty($users)) : ?>
                    <?php foreach ($users as $user) : ?>
                        <tr id="row_<?= $user['id']; ?>">
                            <td><?= htmlspecialchars($user['id']); ?></td>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td>
                                <a href="adminmanager.php?id=<?= $user['id']; ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-edit"></i> Manage
                                </a>
                                <button class="btn btn-danger btn-sm deleteUser" data-id="<?= $user['id']; ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="4" class="text-center">No users found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $(".deleteUser").click(function() {
        let userId = $(this).data("id");
        if (confirm("Are you sure you want to delete this user?")) {
            $.ajax({
                url: "adminuser.php", // Ensure this points to the correct file
                type: "POST",
                data: { delete_id: userId },
                dataType: "json",
                success: function(response) {
                    if (response.status === 200) {
                        $("#row_" + userId).fadeOut("slow", function() {
                            $(this).remove();
                        });
                        alert(response.message);
                    } else {
                        alert("Error: " + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert("AJAX Request failed: " + error);
                }
            });
        }
    });
});
</script>

</body>
</html>
