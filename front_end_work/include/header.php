<link rel="stylesheet" href="style/header.css">
<header>
  <div class="logo">
  <h1 class="logo-text">
    <a href="home.php">
      <span>Sheffield</span> Auto Parts
    </a>
  </h1>
</div>
  <!-- Nav Bar -->
<ul class="navbar">
  <li><a href="home.php">Welcome <?php echo htmlspecialchars($user_data['username']); ?></a></li>
  <li><a href="logout.php">Sign Out</a></li>
</ul>
  

</header>