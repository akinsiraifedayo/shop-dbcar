<!-- 10 -->
<header class="header">
   <div class="flex">
      <a href="index.php" class="logo">Mellodian Community Park</a>
      
      <nav class="navbar">
         <?php
         if (session_status() === PHP_SESSION_NONE) {
            session_start();
         }

         // Check if the user is logged in
         if (isset($_SESSION['user_id'])) {
            // Fetch the user's information (for admin check)
            $user_id = $_SESSION['user_id'];
            $query = "SELECT is_admin FROM users WHERE id = '$user_id'";
            $result = mysqli_query($conn, $query);
            $user = mysqli_fetch_assoc($result);

            if ($user['is_admin'] == 1) {
               // Show admin-related menu items
               echo '<a href="admin_events.php">Add Events</a>';
               echo '<a href="index.php">Preview Events</a>';
               echo '<a href="admin_bookings.php">View Bookings</a>';
               echo '<a href="manage_users.php">Manage Users</a>';
            } else {
               // Show booking-related menu items for non-admins
               echo '<a href="index.php">View Events</a>';
               echo '<a href="my_bookings.php">My Bookings</a>';
            }
         } else {
            // If not logged in, show only public menu items
            echo '<a href="index.php">View Events</a>';
         }
         ?>
      </nav>

      <div class="auth-links">
         <?php
         if (isset($_SESSION['user_id'])) {
            // User is logged in
            echo '<a href="logout.php" class="logout-btn">Logout</a>';
         } else {
            // User is not logged in
            echo '<a href="login.php" class="login-btn">Login</a>';
            echo '<a href="register.php" class="register-btn">Register</a>';
         }
         ?>
      </div>

      <div id="menu-btn" class="fas fa-bars"></div>
   </div>
</header>
