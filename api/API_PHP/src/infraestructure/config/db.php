<?php
     $server = '192.168.100.200';
     $username = 'root';
     $password = 'An8n0pcL.DSB&Lawyers';
     $database = 'dsb&lawyers_dev_admin';
     $db = mysqli_connect($server, $username, $password, $database);
     if (!$db) {
         die("Error de conexión: " . mysqli_connect_error());
     }
     
     mysqli_query($db, "SET NAMES 'utf8'");