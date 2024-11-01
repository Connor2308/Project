<!DOCTYPE html>
<html lang="en">
<head>
    <title>#########</title>
    <link rel="stylesheet" href="css/mobile.css"/>
    <link
      rel="stylesheet"
      href="css/index.css"
      media="only screen and (min-width : 720px)"
    />

</head>
<body>
    <div class="google_translate_element"></div>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script type="text/javascript">function googleTranslateElementInit(){
        new google.translateElement({pagelanguage: 'en'},'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <div class="main">
        <?php include("includes/index_admin.php")
        ?>
        <div class="content">
                <h1>Welcome<span>Admin</span></h1>
                <p class="par"></p>
                <button type="submit"><a href="logout.php" class="btn">Logout</a></button>
            </div>
    </div>
        
    <?php include("includes/footer.php")?>
</body>
</html> 