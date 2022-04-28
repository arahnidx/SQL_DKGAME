<?php

    include('config.php');
    $conn = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $db);
    $connAdmin = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $dbAdmin);
    $mysqli = $conn;
    if (!$mysqli)
    {
        echo "Could not connect to server \n";
        trigger_error(mysqli_connect_error(), E_USER_ERROR);
    }
    
