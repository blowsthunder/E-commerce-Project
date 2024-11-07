<?php

    $db_host = '127.0.0.1';
    $db_name = 'vuk_wear_database';
    $db_user = 'root';
    $db_pass = '';
    $db_port = '3306';

      $dsn = "mysql:host=$db_host;dbname=$db_name;port=$db_port;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
try{
    //create an instance of the PDO class with the required parameters
    $db = new PDO($dsn, $db_user, $db_pass, $options);
    //set pdo error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
     //display seccess message
    
}catch (PDOException $ex){
    //display error message
    echo "Connection failed".$ex->getMessage();
    
}

?>
