<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    $tickets = $_POST['tickets'];

    $event_query = mysqli_query($conn, "SELECT * FROM `events` WHERE id = $event_id");
    if (mysqli_num_rows($event_query) > 0) {
        $event = mysqli_fetch_assoc($event_query);
        $total_price = $event['price'] * $tickets;

        $insert_booking = mysqli_query($conn, "INSERT INTO `bookings` (user_id, event_id, tickets, total_price) 
                                              VALUES ('$user_id', '$event_id', '$tickets', '$total_price')");
        if ($insert_booking) {
            $message = 'Booking successful!';
        } else {
            $message = 'Booking failed. Try again.';
        }
    } else {
        $message = 'Event not found.';
    }
}

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $event_query = mysqli_query($conn, "SELECT * FROM `events` WHERE id = $event_id");
    $event = mysqli_fetch_assoc($event_query);
} else {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <form action="" method="post">
        <h2>Book Tickets for <?php echo $event['name']; ?></h2>
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
        <p>Price per ticket: $<?php echo $event['price']; ?></p>
        <input type="number" name="tickets" placeholder="Number of Tickets" required class="box" min="1">
        <input type="submit" value="Book Now" class="btn">
    </form>
</div>

</body>
</html>
