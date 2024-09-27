<!DOCTYPE html>
<html lang="en">
<head>
    <title>#########</title>
    <link rel="stylesheet" href="css/map.css"/>
    <link rel="stylesheet" href="css/mobile.css"/>
    <link rel="stylesheet" href="css/index.css"/>
    <link
      rel="stylesheet"
      href="css/index.css"
      media="only screen and (min-width : 720px)"
    />
    <script type="text/javascript" src="js/map.js"></script>

</head>
<body>
    <div class="main">
        <?php include("includes/header.php")
        ?>
        <div class="content">
                <h1>########<br><span>Welcomes</span>You</h1>
                <p class="par">This will be a webpage for students and leactures</p>
                
            </div>
    </div>
        <!-- <div class="content">
        <div id="map"></div>
        <script>(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})
        ({key: "AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg", v: "weekly"});</script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkj9scZNcFJ2fWA_N_d5G9WctrJTFFzhM=initMap"></script>
        </div> -->
    <?php include("includes/footer.php")?>
</body>
</html> 