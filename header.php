<header class="header">
   <div class="flex">
      <a href="#" class="logo">Event Booking</a>
      
      <nav class="navbar">
         <a href="admin.php">Add Events</a>
         <a href="all_events.php">View Events</a>
      </nav>

      <div class="auth-links">
         <?php
         if (session_status() === PHP_SESSION_NONE) {
            session_start();
         }

         // Check if the user is logged in
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
