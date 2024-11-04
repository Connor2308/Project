<!DOCTYPE html>
<html lang="en">
<head>
    <title>About Us</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link
      rel="stylesheet"
      href="css/index.css"
      media="only screen and (min-width : 720px)"
    />
    <link rel="stylesheet" href="css/About_us_bot.css" media="only screen and (min-width : 720px)"/>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
  <div class="about-page">
    <div class="main">
        <?php include("includes/header.php")?>
  </div>
    <div class="wrapper">
      <div class="title">Assistant</div>
        <div class="form">
          <div class="box-inbox inbox">
            <div class="icon">
              <i class="fas fa-user"></i>
            </div>
            <div class="msg-header">
              <p>Hello There, What would you like to know?</p>
            </div>
          </div>
        </div>
        <div class="typing-field">
          <div class="input-data">
            <input id="data" type="text" placeholder="Type Something Here?" required>
            <button id="send-btn">Send</button>
          </div>
        </div>
      </div>
      <script>
        $(document).ready(function(){
          $("#send-btn").on("click",function(){
            $value = $("#data").val();
            $msg = '<div class="user-inbox inbox"><div class="msg-header"><p>'+ $value + '</p></div></div>';
            $(".form").append($msg);
            $("#data").val('');
            $.ajax({
              url:'message.php',
              type:'POST',
              data:'text='+$value,
              success:function(result){
                $replay='<div class="bot-inbox"><div class="icon"><i class="fas fa-user"></i></div><div class="msg header"><p>'+ result + '</p></div></div>';
                $(".form").append($replay);
                $(".form").scrollTop($(".form")[0].scrollHeight);
              }
            });
          });
        });
      </script>
    </div>
    <?php include("includes/footer.php")?>
    
</body>
</html> 