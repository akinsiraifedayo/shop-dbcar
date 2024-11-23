<!-- completed -->
<?php
@include 'config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Pagination setup
$limit = 5; // Number of bookings per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get the current page or default to 1
$offset = ($page - 1) * $limit;

// Fetch user's bookings from the database with pagination
$query = "
    SELECT b.id AS booking_id, b.tickets, b.total_price, b.booking_date, e.name AS event_name, e.date_time, e.image
    FROM bookings b
    JOIN events e ON b.event_id = e.id
    WHERE b.user_id = '$user_id'
    ORDER BY b.booking_date DESC
    LIMIT $limit OFFSET $offset
";

$result = mysqli_query($conn, $query);

// Get total number of bookings for pagination calculation
$total_query = "
    SELECT COUNT(*) AS total_bookings
    FROM bookings b
    WHERE b.user_id = '$user_id'
";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_bookings = $total_row['total_bookings'];
$total_pages = ceil($total_bookings / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Make sure this CSS file has your styles -->
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>My Bookings</h2>

    <?php if (mysqli_num_rows($result) > 0) { 
        // Debugging the query result without pagination
        $query = "
        SELECT b.id AS booking_id, b.tickets, b.total_price, b.booking_date, e.name AS event_name, e.date_time, e.image
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        WHERE b.user_id = '$user_id'
        ORDER BY b.booking_date DESC
        ";

        $result = mysqli_query($conn, $query);
        echo mysqli_error($conn); // To show any SQL errors

        ?> 
        
        <div class="event-grid">
        <?php
        if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="event-card">
                <img src="images/events/<?php echo $row['image']; ?>" alt="<?php echo $row['event_name']; ?>" class="event-image">
                <div class="event-details">
                    <h3><?php echo $row['event_name']; ?></h3>
                    <p><strong>Date & Time:</strong> <?php echo date('F j, Y, g:i A', strtotime($row['date_time'])); ?></p>
                    <p><strong>Booking Date:</strong> <?php echo date('F j, Y', strtotime($row['booking_date'])); ?></p>
                    <p><strong>Tickets:</strong> <?php echo $row['tickets']; ?> ticket(s)</p>
                    <p><strong>Total Price:</strong> $<?php echo number_format($row['total_price'], 2); ?> </p>
                </div>
            </div>
        <?php }
        } else {
        echo "<p>No bookings found.</p>";
        } ?>
        </div>




    <?php } ?>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php if ($page > 1) { ?>
            <a href="my_bookings.php?page=<?php echo $page - 1; ?>">Previous</a>
        <?php } ?>
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <a href="my_bookings.php?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php } ?>
        <?php if ($page < $total_pages) { ?>
            <a href="my_bookings.php?page=<?php echo $page + 1; ?>">Next</a>
        <?php } ?>
    </div>
</div>

<script src="js/script.js"></script>

</body>
</html>
