<?php
@include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    $tickets = $_POST['tickets'];
    $adult_seat = $_POST['adult_seat'];
    $adult_photo = isset($_FILES['adult_photo']) ? $_FILES['adult_photo']['name'] : null; // Check if adult photo exists

    // Fetch event details
    $event_query = mysqli_query($conn, "SELECT * FROM `events` WHERE id = $event_id");
    if (mysqli_num_rows($event_query) > 0) {
        $event = mysqli_fetch_assoc($event_query);
        $total_price = $event['price'] * $tickets;

        // Validation checks
        if ($tickets > 8 && $event['is_supervised'] == 1) {
            $message = 'You cannot book more than 8 tickets per transaction for supervised events.';
        } elseif ($event['is_supervised'] == 1 && $adult_seat < 1) {
            $message = 'At least one adult seat must be purchased for events requiring supervision.';
        } elseif ($event['is_supervised'] == 1 && empty($adult_photo)) {
            $message = 'An adult photo is required for events requiring supervision.';
        } elseif ($event['is_supervised'] == 0 && $adult_seat > 0) {
            // For unsupervised events, allow adult seats to be zero, but not greater than zero
            $message = 'Adult seats should be zero for unsupervised events.';
        } else {
            // Process the booking
            // If an adult photo is uploaded, save it
            if (!empty($adult_photo)) {
                $target_dir = "uploads/adult_photos/";
                $target_file = $target_dir . basename($adult_photo);
                move_uploaded_file($_FILES["adult_photo"]["tmp_name"], $target_file);
            }

            // Insert booking into database
            $insert_booking = mysqli_query($conn, "INSERT INTO `bookings` (user_id, event_id, tickets, adult_seat, total_price, adult_photo) 
                                                  VALUES ('$user_id', '$event_id', '$tickets', '$adult_seat', '$total_price', '$adult_photo')");
            if ($insert_booking) {
                $message = 'Booking successful!';
            } else {
                $message = 'Booking failed. Try again.';
            }
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
    <form action="" method="post" enctype="multipart/form-data">
        <h2>Book Tickets for <?php echo $event['name']; ?></h2>
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
        <p>Price per ticket: $<?php echo $event['price']; ?></p>

        <label for="tickets">Number of Tickets:</label>
        <input type="number" name="tickets" id="tickets" placeholder="Number of Tickets" required class="box" min="1">

        <label for="adult_seat">Number of Adult Seats (if required):</label>
        <input type="number" name="adult_seat" id="adult_seat" placeholder="Adult Seats" required class="box" min="0">

        <?php if ($event['is_supervised'] == 1) { ?>
            <label for="adult_photo">Upload Adult Photo (Required for supervision):</label>
            <input type="file" name="adult_photo" id="adult_photo" accept="image/*" class="box" required>
        <?php } ?>

        <input type="submit" value="Book Now" class="btn">
    </form>
</div>

</body>
</html>
