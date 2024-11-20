<!-- 3 -->
<?php
@include 'config.php';
session_start();

// Admin Protection
@include 'admin_protected.php';
check_admin();

// Handle delete action
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $delete_query = "DELETE FROM users WHERE id = $user_id";
    mysqli_query($conn, $delete_query);
    header('Location: manage_users.php');
    exit;
}

// Handle update action
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $update_query = "UPDATE users SET 
                        name = '$name', 
                        email = '$email', 
                        phone = '$phone', 
                        is_admin = $is_admin 
                     WHERE id = $user_id";
    mysqli_query($conn, $update_query);
    header('Location: manage_users.php');
    exit;
}

// Pagination settings
$limit = 10; // Number of users per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit;

// Fetch the total number of users
$total_query = "SELECT COUNT(*) AS total FROM users";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_users = $total_row['total'];

// Calculate the total number of pages
$total_pages = ceil($total_users / $limit);

// Fetch the users for the current page
$query = "SELECT * FROM users LIMIT $limit OFFSET $offset";
$users_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        // Toggle between view and edit mode
        function toggleEdit(userId) {
            const viewElement = document.getElementById('view-' + userId);
            const editElement = document.getElementById('edit-' + userId);
            viewElement.classList.toggle('hidden');
            editElement.classList.toggle('hidden');
        }
    </script>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Manage Users</h2>
    
    <div class="event-grid">
        <?php if (mysqli_num_rows($users_result) > 0) { 
            while ($row = mysqli_fetch_assoc($users_result)) { 
                $userId = $row['id']; ?>
                <div class="event-card">
                    <!-- View Mode -->
                    <div id="view-<?php echo $userId; ?>">
                        <h3><?php echo $row['name']; ?></h3>
                        <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $row['phone'] ?? 'Not provided'; ?></p>
                        <p><strong>Created At:</strong> <?php echo date('F j, Y', strtotime($row['created_at'])); ?></p>
                        <p><strong>Admin:</strong> <?php echo $row['is_admin'] ? 'Yes' : 'No'; ?></p>
                        <div class="admin-actions">
                            <button class="btn" onclick="toggleEdit(<?php echo $userId; ?>)">Edit</button>
                            <a href="manage_users.php?delete=<?php echo $userId; ?>" 
                               class="btn delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div id="edit-<?php echo $userId; ?>" class="hidden">
                        <form method="post" action="">
                            <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                            <label for="name-<?php echo $userId; ?>"><strong>Name:</strong></label>
                            <input type="text" id="name-<?php echo $userId; ?>" name="name" value="<?php echo $row['name']; ?>" required>
                            
                            <label for="email-<?php echo $userId; ?>"><strong>Email:</strong></label>
                            <input type="email" id="email-<?php echo $userId; ?>" name="email" value="<?php echo $row['email']; ?>" required>
                            
                            <label for="phone-<?php echo $userId; ?>"><strong>Phone:</strong></label>
                            <input type="text" id="phone-<?php echo $userId; ?>" name="phone" value="<?php echo $row['phone']; ?>">

                            <label for="is_admin-<?php echo $userId; ?>"><strong>Admin:</strong></label>
                            <input type="checkbox" id="is_admin-<?php echo $userId; ?>" name="is_admin" <?php echo $row['is_admin'] ? 'checked' : ''; ?>>

                            <div class="admin-actions">
                                <button type="submit" name="update_user" class="btn">Save</button>
                                <button type="button" class="btn" onclick="toggleEdit(<?php echo $userId; ?>)">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
        <?php } 
        } else { ?>
            <p>No users found.</p>
        <?php } ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <div class="pagination-buttons">
            <?php if ($page > 1) { ?>
                <a href="manage_users.php?page=<?php echo $page - 1; ?>" class="prev-btn">Prev</a>
            <?php } ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="manage_users.php?page=<?php echo $i; ?>" class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php } ?>

            <?php if ($page < $total_pages) { ?>
                <a href="manage_users.php?page=<?php echo $page + 1; ?>" class="next-btn">Next</a>
            <?php } ?>
        </div>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>
