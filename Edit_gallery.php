<?php
    include 'functions.php';
    $pdo = pdo_connect_msql();
    $stmt = $pdo->query('SELECT * FROM images ORDER by uploaded_date DESC');
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
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
        <div class="content home">
                <h2>Gallery</h2>
                <p>Welcome Admin to the Gallery page! You can View and edit the lists of uploaded images below</p>
                <a href="upload.php" class="upload-images">Upload Images</a>
                <div class="images">
                    <?php foreach ($images as $image): ?>
                    <?php if (file_exists($image['filepath'])): ?>
                    <a href="#">
                        <img src="<?=$image['filepath']?>" alt="<?=$image['description']?>" data-id="<?=$image['id']?>" data-title="<?=$image['title']?>" width="300" height="200">
                        <span><?=$image['description']?></span>
                    </a>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="image-popup"></div>
            <script>
            // Container we'll use to output the image
            let image_popup = document.querySelector('.image-popup');
            // Iterate the images and apply the onclick event to each individual image
                document.querySelectorAll('.images a').forEach(img_link => {
	                img_link.onclick = e => {
		                e.preventDefault();
		                let img_meta = img_link.querySelector('img');
		                let img = new Image();
		                img.onload = () => {
			            // Create the pop out image
			            image_popup.innerHTML = `
				        <div class="con">
					    <h3>${img_meta.dataset.title}</h3>
					    <p>${img_meta.alt}</p>
					    <img src="${img.src}" width="${img.width}" height="${img.height}">
					    <a href="delete.php?id=${img_meta.dataset.id}" class="trash" title="Delete Image"><i class="fas fa-trash fa-xs"></i></a>
				        </div>
			        `;
			        image_popup.style.display = 'flex';
		        };
		        img.src = img_meta.src;
	        };
        });
    // Hide the image popup container, but only if the user clicks outside the image
    image_popup.onclick = e => {
	    if (e.target.className == 'image-popup') {
		    image_popup.style.display = "none";
	    }
    };
    </script>
    </div>
        
    <?php include("includes/footer.php")?>
</body>
</html> 