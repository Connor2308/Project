<?php
require 'db_conn.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>#########</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/mobile.css"/>
    <link
      rel="stylesheet"
      href="css/index.css"
      media="only screen and (min-width : 720px)"
    />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="main">
        <?php include("includes/index_admin.php")
        ?>
        <div class="content">
                <h1>List of Users</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Firstname</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>User_Type</th>
                        </tr>
                    </thead>

                    <tbody>
                     <?php
                     $servername = "localhost";
                     $username = "root";
                     $password = "";
                     $database = "user_db";

                     $connection = new mysqli($servername, $username, $password, $database);

                     if ($connection->connect_error){
                        die("Connection failed: " . $connection->connect_error);
                     }

                     $sql = "SELECT * FROM user_form";
                     $result = $connection->query($sql);

                     if(!$result){
                        die("Invaild query: " .$connection->error);
                     }
                     while($row = $result->FETCH_ASSOC()){
                        echo "<tr>
                            <td>" .$row["id"] . "</td>
                            <td>" .$row["name"] . "</td>
                            <td>" .$row["email"] . "</td>
                            <td>" .$row["password"] . "</td>
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