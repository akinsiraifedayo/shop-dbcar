<?php

@include 'config.php'; // Database configuration file

// Add Event
if (isset($_POST['add_event'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $date_time = $_POST['date_time'];
    $venue = $_POST['venue'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    $insert_query = mysqli_query($conn, "INSERT INTO `events`(name, description, date_time, venue, price, image) 
        VALUES('$name', '$description', '$date_time', '$venue', '$price', '$image')");

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
    $delete_query = mysqli_query($conn, "DELETE FROM `events` WHERE id = $delete_id");

    if ($delete_query) {
        header('location:admin.php');
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
    $venue = $_POST['venue'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    if (!empty($image)) {
        $update_query = mysqli_query($conn, "UPDATE `events` 
            SET name = '$name', description = '$description', date_time = '$date_time', venue = '$venue', price = '$price', image = '$image' 
            WHERE id = '$update_id'");
        if ($update_query) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Event updated successfully!';
        } else {
            $message[] = 'Could not update the event.';
        }
    } else {
        $update_query = mysqli_query($conn, "UPDATE `events` 
            SET name = '$name', description = '$description', date_time = '$date_time', venue = '$venue', price = '$price' 
            WHERE id = '$update_id'");
        if ($update_query) {
            $message[] = 'Event updated successfully!';
        } else {
            $message[] = 'Could not update the event.';
        }
    }
    header('location:admin.php');
}

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
            <input type="text" name="name" placeholder="Event Name" class="box" required>
            <textarea name="description" placeholder="Event Description" class="box" required></textarea>
            <input type="datetime-local" name="date_time" class="box" required>
            <input type="text" name="venue" placeholder="Event Venue" class="box" required>
            <input type="number" name="price" min="0" placeholder="Event Price" class="box" required>
            <input type="file" name="image" accept="image/png, image/jpg, image/jpeg" class="box" required>
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
                    <th>Venue</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_events = mysqli_query($conn, "SELECT * FROM `events`");
                if (mysqli_num_rows($select_events) > 0) {
                    while ($row = mysqli_fetch_assoc($select_events)) {
                ?>
                <tr>
                    <td><img src="./uploaded_img/<?php echo $row['image']; ?>" height="100" alt=""></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['date_time']; ?></td>
                    <td><?php echo $row['venue']; ?></td>
                    <td>$<?php echo $row['price']; ?></td>
                    <td>
                        <a href="admin.php?edit=<?php echo $row['id']; ?>" class="option-btn">Edit</a>
                        <a href="admin.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="7" class="empty">No events added</td></tr>';
                }
                ?>
            </tbody>
        </table>
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
            <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>">
            <input type="text" name="name" value="<?php echo $fetch_edit['name']; ?>" class="box" required>
            <textarea name="description" class="box" required><?php echo $fetch_edit['description']; ?></textarea>
            <input type="datetime-local" name="date_time" value="<?php echo date('Y-m-d\TH:i', strtotime($fetch_edit['date_time'])); ?>" class="box" required>
            <input type="text" name="venue" value="<?php echo $fetch_edit['venue']; ?>" class="box" required>
            <input type="number" name="price" value="<?php echo $fetch_edit['price']; ?>" class="box" required>
            <input type="file" name="image" class="box">
            <input type="submit" value="Update Event" name="update_event" class="btn">
        </form>
        <?php
            }
        }
        ?>
    </section>

</div>

<script src="js/script.js"></script>

</body>
</html>
