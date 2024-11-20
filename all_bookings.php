<?php

@include 'config.php'; // Include the database connection

// Pagination setup
$limit = 6;  // Number of events per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetching events from the database with pagination
$select_events = mysqli_query($conn, "SELECT * FROM `events` LIMIT $offset, $limit");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>All Events</h2>
    
    <div class="event-grid">
        <?php
        if (mysqli_num_rows($select_events) > 0) {
            while ($row = mysqli_fetch_assoc($select_events)) {
        ?>
            <div class="event-card">
                <!-- Assuming you have an image column in the events table -->
                <img src="uploaded_img/<?php echo $row['image']; ?>" alt="<?php echo $row['event_name']; ?>" class="event-image">
                <h3><?php echo $row['name']; ?></h3>
                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($row['date_time'])); ?></p>
                <p><strong>Location:</strong> <?php echo $row['location']; ?></p> <!-- Location from the database -->
                <p><strong>Price:</strong> $<?php echo number_format($row['price'], 2); ?></p>
                
                <!-- Displaying the new seating type -->
                <p><strong>Seating Type:</strong> <?php echo $row['seating_type']; ?></p>
                
                <!-- Displaying supervision requirement -->
                <p><strong>Requires Adult Supervision:</strong> <?php echo ($row['is_supervised'] == 1) ? 'Yes' : 'No'; ?></p>

                <a href="book_ticket.php?id=<?php echo $row['id']; ?>" class="btn">Book Ticket</a>
            </div>
        <?php
            }
        } else {
            echo "<p>No events found.</p>";
        }
        ?>
    </div>

    <?php
    // Get total events count
    $total_events_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `events`");
    $total_events = mysqli_fetch_assoc($total_events_query)['total'];
    $total_pages = ceil($total_events / $limit);

    // Pagination
    echo '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<a href="index.php?page=' . $i . '">' . $i . '</a>';
    }
    echo '</div>';
    ?>

</div>

<script src="js/script.js"></script>

</body>
</html>
