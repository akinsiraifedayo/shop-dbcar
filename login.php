<?php
@include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query the customers table instead of users table
    $user_query = mysqli_query($conn, "SELECT * FROM `customers` WHERE email = '$email'");
    if (mysqli_num_rows($user_query) > 0) {
        $user = mysqli_fetch_assoc($user_query);
        // Verify password using the stored hash
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: index.php');
            exit;
        } else {
            $message = 'Incorrect password. Please try again.';
        }
    } else {
        $message = 'No account found with that email.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <form action="" method="post">
        <h2>Login</h2>
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
        <input type="email" name="email" placeholder="Email Address" required class="box">
        <input type="password" name="password" placeholder="Password" required class="box">
        <input type="submit" value="Login" class="btn">
    </form>
</div>

</body>
</html>
