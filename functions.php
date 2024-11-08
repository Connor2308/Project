<?php
    function pdo_connect_msql(){
        $DATABASE_HOST = 'localhost';
        $DATABASE_USER = 'root';
        $DATABASE_PASS = '';
        $DATABASE_NAME = 'phpgallery';
        try{
            return new PDO('mysql:host=' . $DATABASE_HOST . 'dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
        } catch (PDOException $exception){
            exit('Failed to connect to database!');
        }
    }
?>