<?php
include 'db.php'; // Include PDO connection

// Handle Delete Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $userId = intval($_POST['delete_id']);
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

// Handle Update Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $userId = intval($_POST['update_id']);
    $newUsername = trim($_POST['username']);
    $newEmail = trim($_POST['email']);

    try {
        $stmt = $conn->prepare("UPDATE signup SET username = :username, email = :email WHERE id = :id");
        $stmt->bindParam(':username', $newUsername, PDO::PARAM_STR);
        $stmt->bindParam(':email', $newEmail, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('User updated successfully'); window.location.href = 'adminuser.php';</script>";
        } else {
            echo "<script>alert('Update failed');</script>";
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
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
            margin: 0;
            font-family: Arial, sans-serif;
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
        .sidebar a:hover {
            background: #495057;
        }
        .sidebar .active {
            background: #007bff;
        }
        .sidebar .logo {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            padding-bottom: 10px;
        }
        .main-content {
            margin-left: 260px; /* Adjusted for sidebar width */
            padding: 20px;
            width: calc(100% - 260px);
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <i class="fas fa-user-shield"></i> Admin Panel
    </div>
    <a href="../frontend/admindashboard.html" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="adminuser.php"><i class="fas fa-users"></i> User Management</a>
    <a href="#"><i class="fas fa-file-alt"></i> Content</a>
    <a href="#"><i class="fas fa-question-circle"></i> Evaluation Questions</a>
    <a href="#"><i class="fas fa-cogs"></i> Settings</a>
    <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <div class="container mt-5">
        <h2 class="mb-4">User Management</h2>
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
                <?php foreach ($users as $user) : ?>
                    <tr id="row_<?= $user['id']; ?>">
                        <td><?= htmlspecialchars($user['id']); ?></td>
                        <td><?= htmlspecialchars($user['username']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td>
                            <button class="btn btn-success btn-sm manageUser" data-id="<?= $user['id']; ?>" data-username="<?= $user['username']; ?>" data-email="<?= $user['email']; ?>">
                                Manage
                            </button>
                            <button class="btn btn-danger btn-sm deleteUser" data-id="<?= $user['id']; ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Manage User Modal -->
<div class="modal fade" id="manageUserModal" tabindex="-1" aria-labelledby="manageUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageUserModalLabel">Manage User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="update_id" id="updateUserId">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="updateUsername" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="updateEmail" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Delete User
    $(".deleteUser").click(function() {
        let userId = $(this).data("id");
        if (confirm("Are you sure you want to delete this user?")) {
            $.ajax({
                url: "user_management.php",
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
                }
            });
        }
    });

    // Manage User - Show Modal and Fill Data
    $(".manageUser").click(function() {
        $("#updateUserId").val($(this).data("id"));
        $("#updateUsername").val($(this).data("username"));
        $("#updateEmail").val($(this).data("email"));
        $("#manageUserModal").modal("show");
    });
});
</script>

</body>
</html>
