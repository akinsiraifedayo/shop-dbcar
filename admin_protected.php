<!-- completed -->
<?php
session_start();

function check_admin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {

        // // Option 1: Redirect to the index page
        // header('Location: index.php');
        // exit;

        // Option 2: Throw an unauthorized error
        http_response_code(403);
        echo "Unauthorized access. Admins only.";
        exit;
    }
}
