<?php
require_once("config.php");
$queryHotels = "SELECT * FROM hotelandhomestays";
$resultHotels = $conn->query($queryHotels);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Cantor Collage</title>
  <link rel="stylesheet" href="css/map.css"/>
  <link rel="stylesheet" href="css/mobile.css" />
  <link rel="stylesheet" href="css/index.css"/>
  <script type="module" src="js/map.js"></script>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC68DglTmjWDwWer3IbxnSfHzPkLNLDaOw&callback=initMap" async defer></script>
  <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
  

</head>
<body>
  <?php include("includes/header.php")?>
  <!-- <input id="pac_input" class="controls" type="text" placeholder="Search Box"/> -->
  <div class="main"></div>
  <div id="map"></div>
  <?php
  echo "<script>";
  echo "let hotelData = ["; //declaring hotel data variable
  while ($row = $resultHotels->fetch_object()) {
    $hotelName = $row->hotel_name;
    $lat = $row->latitude;
    $lng = $row->longitude;
    $hId = $row->hotel_id;
    echo "{ name: '$hotelName', lat: $lat, lng: $lng, hotelId: $hId},";
  }
  echo "];";
  echo "</script>";
  ?>
    
</body>
</html> 