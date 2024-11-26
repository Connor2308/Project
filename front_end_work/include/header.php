<link rel="stylesheet" href="style/header.css">
<header>
  <div class="logo">
  <h1 class="logo-text">
    <a href="home.php">
      <span>Sheffield</span> Auto Parts
    </a>
  </h1></div>

  <!-- Search Bar
  <form class="search-bar">
    <input type="text" name="" placeholder="Search parts..." required>
    <button type="search-submit" aria-label="Search">Search</button>
  </form> -->

  <!-- Nav Bar -->
  <ul class="navbar">
    <li><a href="adminhome.php">Welcome <?php echo htmlspecialchars($user_data['username']); ?></a></li>
    <li><a href="manage_account.php">Manage Account</a></li>
    <li><a href="logout.php">Sign Out</a></li>
  </ul>

</header>