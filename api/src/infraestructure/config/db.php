<?php
     $server = 'localhost';
     $username = 'root';
     $password = '12345678';
     $database = 'dsb&lawyers_dev_admin';
     $db = mysqli_connect($server, $username, $password, $database);
     if (!$db) {
         die("Error de conexión: " . mysqli_connect_error());
     }
     
     mysqli_query($db, "SET NAMES 'utf8'");