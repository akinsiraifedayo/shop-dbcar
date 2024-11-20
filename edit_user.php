<?php
@include 'config.php';
session_start();

// Check if the admin is logged in
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
//     header('Location: login.php');
//     exit;
// }

// Get user ID
if (!isset($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}

$user_id = $_GET['id'];

// Fetch user data
$user_query = "SELECT * FROM customers WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

// Update user details
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $update_query = "UPDATE customers SET 
                        name = '$name', 
                        email = '$email', 
                        phone = '$phone', 
                        is_admin = $is_admin 
                     WHERE id = $user_id";
    mysqli_query($conn, $update_query);

    header('Location: manage_users.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Edit User</h2>
    
    <div class="event-card">
        <form action="" method="post">
            <label for="name"><strong>Name:</strong></label>
            <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
            
            <label for="email"><strong>Email:</strong></label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
            
            <label for="phone"><strong>Phone:</strong></label>
            <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>">

            <label for="is_admin"><strong>Set as Admin:</strong></label>
            <input type="checkbox" id="is_admin" name="is_admin" <?php echo $user['is_admin'] ? 'checked' : ''; ?>>
            
            <button type="submit" name="update" class="btn">Update</button>
        </form>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>
