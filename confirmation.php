<?php
@include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the form
    $event_id = $_POST['event_id'];
    $quantity = $_POST['quantity'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $payment_method = $_POST['payment_method'];

    // Fetch the event details for display and calculation
    $event_query = mysqli_query($conn, "SELECT * FROM `events` WHERE id = $event_id");
    if (mysqli_num_rows($event_query) > 0) {
        $event = mysqli_fetch_assoc($event_query);
        $event_name = $event['name'];
        $ticket_price = $event['price'];
        $total_cost = $ticket_price * $quantity;

        // Insert the purchase into the database
        $insert_purchase = mysqli_query($conn, "INSERT INTO `purchases` (event_id, fullname, email, quantity, total_cost, payment_method) 
            VALUES ('$event_id', '$fullname', '$email', '$quantity', '$total_cost', '$payment_method')");

        if (!$insert_purchase) {
            echo "<script>alert('Failed to save purchase details!'); window.location = 'all_events.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Event not found!'); window.location = 'all_events.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid access!'); window.location = 'all_events.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Confirmation</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <section class="confirmation">
        <h2>Thank You for Your Purchase!</h2>
        <p><strong>Event:</strong> <?php echo $event_name; ?></p>
        <p><strong>Tickets Quantity:</strong> <?php echo $quantity; ?></p>
        <p><strong>Total Cost:</strong> $<?php echo number_format($total_cost, 2); ?></p>
        <p><strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $payment_method)); ?></p>
        <p><strong>Buyer:</strong> <?php echo $fullname; ?> (<?php echo $email; ?>)</p>
        <p>Your ticket(s) have been successfully reserved. Enjoy the event!</p>
    </section>
</div>

</body>
</html>
