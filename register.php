<!-- completed -->
<?php
@include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = $_POST['phone'];

    // Check if the email already exists
    $check_email = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $message = 'Email already exists. Please login instead.';
    } else {
        // Insert the new customer into the database
        $insert_user = mysqli_query($conn, "INSERT INTO `users` (name, email, password, phone) 
                                            VALUES ('$name', '$email', '$password', '$phone')");
        if ($insert_user) {
            $message = 'Registration successful. Please login.';
        } else {
            $message = 'Registration failed. Try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <form action="" method="post">
        <h2>Register</h2>
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
        <input type="text" name="name" placeholder="Full Name" required class="box">
        <input type="email" name="email" placeholder="Email Address" required class="box">
        <input type="password" name="password" placeholder="Password" required class="box">
        <input type="text" name="phone" placeholder="Phone Number" class="box">
        
        <input type="submit" value="Register" class="btn">
    </form>
</div>

</body>
</html>
