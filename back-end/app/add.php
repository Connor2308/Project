<?php

if(isset($_POST['title'])){
    require '../db_conn.php';

    $title = $_POST['title'];

    if(empty($title)){
        header("Location: ../index_user.php?mess=error");
    }else {
        $stmt = $conn->prepare("INSERT INTO todos(title) VALUE(?)");
        $res = $stmt->execute([$title]);

        if($res){
            header("Location: ../index_user.php?mess=success"); 
        }else {
            header("Location: ../index_user.php");
        }
        $conn = null;
        exit();
    }
}else {
    header("Location: ../index_user.php?mess=error");
}