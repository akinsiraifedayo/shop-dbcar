<!-- completed -->
<?php
@include 'config.php';  // Assuming 'config.php' initializes your $conn

session_start();

// Admin Protection
@include 'admin_protected.php';
check_admin();

// Set how many bookings to show per page
$limit = 10;  // Number of bookings per page

// Get the current page from the URL, default to page 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;  // Calculate the offset for SQL query

// Check if $conn is valid
if (!$conn) {
    die('Database connection error: ' . mysqli_connect_error());
}

// Fetch total bookings count for pagination
$count_query = "SELECT COUNT(*) AS total FROM bookings";
$count_result = mysqli_query($conn, $count_query);
if (!$count_result) {
    die('Query failed: ' . mysqli_error($conn));
}
$count_row = mysqli_fetch_assoc($count_result);
$total_bookings = $count_row['total'];
$total_pages = ceil($total_bookings / $limit);  // Calculate total pages

// Fetch bookings for the current page
$query = "SELECT b.id, b.tickets, b.adult_seat, b.total_price, b.booking_date, 
                 e.name AS event_name, e.image AS event_image, 
                 u.name AS customer_name, u.email AS customer_email, 
                 b.adult_photo
          FROM bookings b
          JOIN events e ON b.event_id = e.id
          JOIN users u ON b.user_id = u.id
          LIMIT $limit OFFSET $offset";
$bookings_result = mysqli_query($conn, $query);
if (!$bookings_result) {
    die('Query failed: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Bookings</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>All Bookings</h2>
    
    <div class="event-grid">
        <?php if (mysqli_num_rows($bookings_result) > 0) { 
            while ($row = mysqli_fetch_assoc($bookings_result)) { ?>
                <div class="event-card">
                    <img src="images/events/<?php echo $row['event_image']; ?>" alt="<?php echo $row['event_name']; ?>">
                    <h3><?php echo $row['event_name']; ?></h3>
                    <p><strong>Customer:</strong> <?php echo $row['customer_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $row['customer_email']; ?></p>
                    <p><strong>Tickets:</strong> <?php echo $row['tickets']; ?></p>
                    <p><strong>Adult Seats:</strong> <?php echo $row['adult_seat']; ?></p>
                    <p><strong>Total Price:</strong> $<?php echo number_format($row['total_price'], 2); ?></p>
                    <p><strong>Booking Date:</strong> <?php echo date('F j, Y, g:i A', strtotime($row['booking_date'])); ?></p>
                    <?php if (!empty($row['adult_photo']) && $row['adult_photo'] != 'no-photo') { ?>
                        <p><strong>Adult Photo:</strong></p>
                        <img src="images/adult_photos/<?php echo $row['adult_photo']; ?>" alt="Adult Photo" class="event-image">
                    <?php } else { ?>
                        <p><em>No adult photo required or provided.</em></p>
                    <?php } ?>
                </div>
        <?php } 
        } else { ?>
            <p>No bookings found.</p>
        <?php } ?>
    </div>

    <!-- Pagination -->
    <section class="pagination">
        <div class="pagination-buttons">
            <?php if ($page > 1) { ?>
                <a href="admin_bookings.php?page=<?php echo $page - 1; ?>" class="prev-btn">← Prev</a>
            <?php } ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="admin_bookings.php?page=<?php echo $i; ?>" class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php } ?>

            <?php if ($page < $total_pages) { ?>
                <a href="admin_bookings.php?page=<?php echo $page + 1; ?>" class="next-btn">Next →</a>
            <?php } ?>
        </div>
    </section>

</div>

<script src="js/script.js"></script>
</body>
</html>

