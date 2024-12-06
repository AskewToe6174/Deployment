<?php
     $server = 'database-1.c788yem4g4uo.us-east-2.rds.amazonaws.com';
     $username = 'onlyread';
     $password = '-r4$rEquCo5#?hEwuc$0';
     $database = 'dsb&lawyers_dev_admin';
     $db = mysqli_connect($server, $username, $password, $database);
     if (!$db) {
         die("Error de conexión: " . mysqli_connect_error());
     }
     
     mysqli_query($db, "SET NAMES 'utf8'");