<link rel="stylesheet" href="style/header.css">
<header>
  <div class="logo">
  <h1 class="logo-text">
    <a href="home.php">
      <span>Sheffield</span> Auto Parts
    </a>
  </h1></div>
  <!-- Search Bar -->
  <div class="search-bar">
    <form action="search_results.php" method="GET">
      <input type="text" name="query" placeholder="Search..." required>
      <button type="submit">Search</button>
    </form>
  </div>
  <!-- Nav Bar -->
  <ul class="navbar">
    <li><a href="home.php">Welcome <?php echo htmlspecialchars($user_data['username']); ?></a></li>
    <li><a href="logout.php">Sign Out</a></li>
  </ul>
  

</header>