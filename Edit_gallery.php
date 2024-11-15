<?php
    include 'functions.php';
    $pdo = pdo_connect_msql();
    $stmt = $pdo->query('SELECT * FROM images ORDER by uploaded_date DESC');
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Car repair Website</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/mobile.css"/>
    <link
      rel="stylesheet"
      href="css/index.css"
      media="only screen and (min-width : 720px)"
    />
</head>
<body>
    <div class="main">
        <?php include("includes/index_admin.php")
        ?>
        <div class="content">
                <h2>Gallery</h2>
                <p></p>
    </div>
        
    <?php include("includes/footer.php")?>
</body>
</html> 