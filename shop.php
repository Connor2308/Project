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
    <script src="https://cdn.anychart.com/releases/8.8.0/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.8.0/js/anychart-data-adapter.min.js"></script>

</head>
<?php//move this php to the connections.php so the graph know the selection choice
      $myquery = "
      SELECT  * FROM  `data`
      ";
    $query = mysql_query($myquery);
   
    if ( ! $query ) {
      echo mysql_error();
      die;
    }
   
    // encode data to json format
    echo json_encode($data);  
   
    // close connection
    mysql_close($server);
?>
<body>
    <div class="main">
        <?php include("includes/header.php")
        ?>
        <div class="content"></div>
        <script>//this script is for the bar chart
            any.chart.onDocumentReady(function(){
            anychart.data.loadjsonFile("connection.php", function(data){
                chart = anychart.bar(data);
                chart.container("content");//where the contents is change if the container name is different
                chart.draw();
            });
            });
        </script>
    </div>
        
    <?php include("includes/footer.php")?>
</body>
</html> 