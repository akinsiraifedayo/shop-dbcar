<?php
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    echo "Welcome, Admin!";
} else {
    echo "Welcome, User!";
}
?>