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
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <div class="main">
        <?php include("includes/index_admin.php")
        ?>
        <div class="content">
                <h1>Users<span>Invoices</span></h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Firstname</th>
                            <th>User_Type</th>
                        </tr>
                    </thead>

                    <tbody>
                     <?php
                     $servername = "localhost";
                     $username = "root";
                     $password = "";
                     $database = "product_db";

                     $connection = new mysqli($servername, $username, $password, $database);

                     if ($connection->connect_error){
                        die("Connection failed: " . $connection->connect_error);
                     }

                     $sql = "SELECT * FROM product_form";
                     $result = $connection->query($sql);

                     if(!$result){
                        die("Invaild query: " .$connection->error);
                     }
                     while($row = $result->FETCH_ASSOC()){
                        echo "<tr>
                            <td>" .$row["id"] . "</td>
                            <td>" .$row["product"] . "</td>
                            <td>" .$row["firstname"] . "</td>
                            <td>" .$row["user_type"] . "</td>
                            <td>
                                <a class='btn btn-primary btn-sm' href='update'>Update</a>
                                <a class='btn btn-danger btn-sm' href='delete'>Delete</a>
                            </td>
                        </tr>";
                    }
                     ?>   
                         
                    </tbody>
                </table>
            </div>
    </div>
        
    <?php include("includes/footer.php")?>
</body>
</html> 