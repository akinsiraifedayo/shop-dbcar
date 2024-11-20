<?php

@include 'config.php'; // Database configuration file

// Add Event
if (isset($_POST['add_event'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $date_time = $_POST['date_time'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $is_supervised = isset($_POST['is_supervised']) ? 1 : 0;  // Check if supervision is required
    $seating_type = $_POST['seating_type'];  // "With Tables" or "Without Tables"
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    // Insert event into the database with is_supervised and seating_type fields
    $insert_query = mysqli_query($conn, "INSERT INTO `events`(name, description, date_time, location, price, image, is_supervised, seating_type) 
        VALUES('$name', '$description', '$date_time', '$location', '$price', '$image', '$is_supervised', '$seating_type')");

    if ($insert_query) {
        move_uploaded_file($image_tmp_name, $image_folder);
        $message[] = 'Event added successfully!';
    } else {
        $message[] = 'Could not add the event.';
    }
}


// Delete Event
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    // Fetch image path before deletion
    $select_image_query = mysqli_query($conn, "SELECT image FROM `events` WHERE id = $delete_id");
    $row = mysqli_fetch_assoc($select_image_query);
    $image_path = 'uploaded_img/' . $row['image'];

    // Delete event from database
    $delete_query = mysqli_query($conn, "DELETE FROM `events` WHERE id = $delete_id");

    if ($delete_query) {
        // Delete image file from server
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        header('location:admin_events.php');
        $message[] = 'Event deleted successfully!';
    } else {
        $message[] = 'Could not delete the event.';
    }
}

// Update Event
if (isset($_POST['update_event'])) {
    $update_id = $_POST['update_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $date_time = $_POST['date_time'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $is_supervised = isset($_POST['is_supervised']) ? 1 : 0;  // Check if supervision is required
    $seating_type = $_POST['seating_type'];  // "With Tables" or "Without Tables"
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    if (!empty($image)) {
        // Update event with new image and seating type
        $update_query = mysqli_query($conn, "UPDATE `events` 
            SET name = '$name', description = '$description', date_time = '$date_time', location = '$location', price = '$price', image = '$image', is_supervised = '$is_supervised', seating_type = '$seating_type'
            WHERE id = '$update_id'");
        if ($update_query) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Event updated successfully!';
        } else {
            $message[] = 'Could not update the event.';
        }
    } else {
        // Update event without changing the image but with new seating type
        $update_query = mysqli_query($conn, "UPDATE `events` 
            SET name = '$name', description = '$description', date_time = '$date_time', location = '$location', price = '$price', is_supervised = '$is_supervised', seating_type = '$seating_type'
            WHERE id = '$update_id'");
        if ($update_query) {
            $message[] = 'Event updated successfully!';
        } else {
            $message[] = 'Could not update the event.';
        }
    }
    header('location:admin_events.php');
}


// Number of events per page
$events_per_page = 2;

// Get the current page number from the query string, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting point (offset) for the SQL query
$offset = ($page - 1) * $events_per_page;
// Fetch total number of events to calculate total pages
$total_events_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `events`");
$total_events = mysqli_fetch_assoc($total_events_query)['total'];
$total_pages = ceil($total_events / $events_per_page);

// Fetch events for the current page
$select_events = mysqli_query($conn, "SELECT * FROM `events` LIMIT $events_per_page OFFSET $offset");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Events</title>

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

<section class="add-event-form">
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Add a New Event</h3>
        
        <!-- Event Name -->
        <div class="form-group">
            <label for="name">Event Name</label>
            <input type="text" name="name" id="name" placeholder="Event Name" class="box" required>
        </div>
        
        <!-- Event Description -->
        <div class="form-group">
            <label for="description">Event Description</label>
            <textarea name="description" id="description" placeholder="Event Description" class="box" required></textarea>
        </div>
        
        <!-- Date and Time -->
        <div class="form-group">
            <label for="date_time">Event Date & Time</label>
            <input type="datetime-local" name="date_time" id="date_time" class="box" required>
        </div>
        
        <!-- Event Location -->
        <div class="form-group">
            <label for="location">Event Location</label>
            <input type="text" name="location" id="location" placeholder="Event Location" class="box" required>
        </div>
        
        <!-- Event Price -->
        <div class="form-group">
            <label for="price">Event Price</label>
            <input type="number" name="price" id="price" min="0" placeholder="Event Price" class="box" required>
        </div>
        
        <!-- Event Image -->
        <div class="form-group">
            <label for="image">Event Image</label>
            <input type="file" name="image" id="image" accept="image/png, image/jpg, image/jpeg" class="box" required>
        </div>
        
        <!-- Supervision Checkbox -->
        <div class="form-group">
            <label for="is_supervised">Requires Adult Supervision</label>
            <input type="checkbox" name="is_supervised" id="is_supervised">
        </div>
        
        <!-- Event Seating Type -->
        <div class="form-group">
            <label for="seating_type">Seating Type</label>
            <select name="seating_type" id="seating_type" class="box" required>
                <option value="With Tables">With Tables</option>
                <option value="Without Tables">Without Tables</option>
            </select>
        </div>
        
        <input type="submit" value="Add Event" name="add_event" class="btn">
    </form>
</section>


<section class="display-events">
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Date & Time</th>
                <th>Location</th>
                <th>Price</th>
                <th>Supervision</th>
                <th>Seating Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($select_events) > 0) {
                while ($row = mysqli_fetch_assoc($select_events)) {
                    $is_supervised = $row['is_supervised'] ? 'Yes' : 'No';
                    $seating_type = $row['seating_type'];
            ?>
            <tr>
                <td><img src="./uploaded_img/<?php echo $row['image']; ?>" height="100" alt=""></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['date_time']; ?></td>
                <td><?php echo $row['location']; ?></td>
                <td>$<?php echo $row['price']; ?></td>
                <td><?php echo $is_supervised; ?></td>
                <td><?php echo $seating_type; ?></td>
                <td>
                    <a href="admin_events.php?edit=<?php echo $row['id']; ?>" class="option-btn">Edit</a>
                    <a href="admin_events.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="9" class="empty">No events added</td></tr>';
            }
            ?>
        </tbody>
    </table>
</section>

    <!-- Pagination controls -->
    <section class="pagination">
        <div class="pagination-buttons">
            <?php if ($page > 1) { ?>
                <a href="admin_events.php?page=<?php echo $page - 1; ?>" class="prev-btn">← Prev</a>
            <?php } ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="admin_events.php?page=<?php echo $i; ?>" class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php } ?>

            <?php if ($page < $total_pages) { ?>
                <a href="admin_events.php?page=<?php echo $page + 1; ?>" class="next-btn">Next →</a>
            <?php } ?>
        </div>
    </section>


    <section class="edit-event-form-container">
    <?php
    if (isset($_GET['edit'])) {
        $edit_id = $_GET['edit'];
        $edit_query = mysqli_query($conn, "SELECT * FROM `events` WHERE id = $edit_id");
        if (mysqli_num_rows($edit_query) > 0) {
            $fetch_edit = mysqli_fetch_assoc($edit_query);
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Edit Event</h3>
        
        <!-- Hidden ID for Update -->
        <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>">
        
        <!-- Event Name -->
        <div class="form-group">
            <label for="name">Event Name</label>
            <input type="text" name="name" id="name" value="<?php echo $fetch_edit['name']; ?>" class="box" required>
        </div>
        
        <!-- Event Description -->
        <div class="form-group">
            <label for="description">Event Description</label>
            <textarea name="description" id="description" class="box" required><?php echo $fetch_edit['description']; ?></textarea>
        </div>
        
        <!-- Date and Time -->
        <div class="form-group">
            <label for="date_time">Event Date & Time</label>
            <input type="datetime-local" name="date_time" id="date_time" value="<?php echo date('Y-m-d\TH:i', strtotime($fetch_edit['date_time'])); ?>" class="box" required>
        </div>
        
        <!-- Event Location -->
        <div class="form-group">
            <label for="location">Event Location</label>
            <input type="text" name="location" id="location" value="<?php echo $fetch_edit['location']; ?>" class="box" required>
        </div>
        
        <!-- Event Price -->
        <div class="form-group">
            <label for="price">Event Price</label>
            <input type="number" name="price" id="price" value="<?php echo $fetch_edit['price']; ?>" class="box" required>
        </div>
        
        <!-- Event Image -->
        <div class="form-group">
            <label for="image">Event Image</label>
            <input type="file" name="image" id="image" class="box">
        </div>
        
        <!-- Supervision Checkbox -->
        <div class="form-group">
            <label for="is_supervised">Requires Adult Supervision</label>
            <input type="checkbox" name="is_supervised" id="is_supervised" <?php echo ($fetch_edit['is_supervised'] == 1) ? 'checked' : ''; ?>>
        </div>
        
        <!-- Event Seating Type -->
        <div class="form-group">
            <label for="seating_type">Seating Type</label>
            <select name="seating_type" id="seating_type" class="box" required>
                <option value="With Tables" <?php echo ($fetch_edit['seating_type'] == 'With Tables') ? 'selected' : ''; ?>>With Tables</option>
                <option value="Without Tables" <?php echo ($fetch_edit['seating_type'] == 'Without Tables') ? 'selected' : ''; ?>>Without Tables</option>
            </select>
        </div>
        
        <input type="submit" value="Update Event" name="update_event" class="btn">
    </form>
    <?php
        }
    }
    ?>
</section>

</div>

</body>
</html>
