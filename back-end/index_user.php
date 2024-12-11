<?php
require 'db_conn.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User index</title>
    <link rel="stylesheet" href="css/index2.css">
</head>
<body>
    
    <div class="main-section">
        
        <div class="add-section">
            <form action="app/add.php" method="POST" autocomplete="off">
                <?php if(isset($_GET['mess']) && $_GET['mess'] == 'error') { ?>
                    <input type="text" name="title" style="border-color: #ff6666" placeholder="This field is required" />
                <button type="submit">Add &nbsp; <span>&#43;</span></button>
                

                <?php } else { ?>
                <input type="text" name="title" placeholder="What do you need?" />
                <button type="submit">Add &nbsp; <span>&#43;</span></button>
                <button type="submit"><a href="home_user.php" class="btn">Home &nbsp; <span>&#43;</span></a></button>
                <button type="submit"><a href="logout.php" class="btn">logout &nbsp; <span>&#43;</span></a></button>
                <?php } ?>
            </form>
        </div>
        <?php
            $todos = $conn->query("SELECT * FROM todos ORDER BY id DESC");
        ?>
        <div class="show-todo-section">
            <?php if($todos->rowCount() === 0){?>
                <div class="todo-item">
                    <div class="empty">
                        <img src="images/f.png" width="100%" />
                        <img src="images/Ellipsis.gif" width="80px"> 
                    </div>
                </div>
            <?php } ?>

            <?php while($todo = $todos->fetch(PDO::FETCH_ASSOC)) { ?>
                <div class="todo-item">
                    <span id="<?php echo $todo['id']; ?>" class="remove-to-do">x</span>
                    <?php if($todo['checked']) {?>
                        <input type="checkbox" class="check-box" checked>
                        <h2 class="checked"><?php echo $todo['title']?></h2>
                        
                    <?php }else { ?>
                        <input type="checkbox" class="check-box" />
                        <h2><?php echo $todo['title']?></h2>
                    <?php } ?>
                    <br>
                    <small>Created: <?php echo $todo['date_time']?> </small>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="js/jquery-3.2.1.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.remove-to-do').click(function(){
                const id = $(this).attr('id');
                
                $.post("app/remove.php", 
                      {
                          id: id
                      },
                      (data)  => {
                         if(data){
                             $(this).parent().hide(600);
                         }
                      }
                );
            });

            $(".check-box").click(function(e){
                const id = $(this).attr('data-todo-id');
                
                $.post('app/check.php', 
                      {
                          id: id
                      },
                      (data) => {
                          if(data != 'error'){
                              const h2 = $(this).next();
                              if(data === '1'){
                                  h2.removeClass('checked');
                              }else {
                                  h2.addClass('checked');
                              }
                          }
                      }
                );
            });
        });
    </script>
</body>
</html>