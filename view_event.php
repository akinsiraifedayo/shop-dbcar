<?php

@include 'config.php'; // Database configuration file

// Check if event ID is set
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Fetch event details from the database
    $select_event = mysqli_query($conn, "SELECT * FROM `events` WHERE id = '$event_id'");
    if (mysqli_num_rows($select_event) > 0) {
        $event = mysqli_fetch_assoc($select_event);
    } else {
        $message[] = 'Event not found';
    }
} else {
    $message[] = 'Invalid event ID';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '<div class="message"><span>' . $msg . '</span> <i class="fas fa-times" onclick="this.parentElement.style.display = `none`;"></i></div>';
    }
}
?>

<?php include 'header.php'; ?>

<div class="container">

    <?php if (isset($event)) { ?>
    <section class="event-details">
        <h2><?php echo $event['name']; ?></h2>

        <div class="event-info">
            <img src="uploaded_img/<?php echo $event['image']; ?>" alt="<?php echo $event['name']; ?>" class="event-image" />
            
            <div class="event-description">
                <p><strong>Description:</strong> <?php echo $event['description']; ?></p>
                <p><strong>Date & Time:</strong> <?php echo date('F j, Y, g:i A', strtotime($event['date_time'])); ?></p>
                <p><strong>Venue:</strong> <?php echo $event['venue']; ?></p>
                <p><strong>Price:</strong> $<?php echo $event['price']; ?>/-</p>
            </div>
        </div>
    </section>

    <section class="event-action">
        <a href="buy_ticket.php?event_id=<?php echo $event['id']; ?>" class="btn">Buy Ticket</a>
    </section>

    <?php } ?>

</div>

<script src="js/script.js"></script>

</body>
</html>
