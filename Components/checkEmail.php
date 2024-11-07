<?php
include_once '../includes/db_connection.php';
// Check if the user is authenticated (logged in);
if(isset($_POST["email"])){
    try{
        $sel = $db->prepare("select * from users where email=? limit 1");
        $sel->execute(array($_POST["email"]));
        if ($sel) {
            $tab_email = $sel->fetch(PDO::FETCH_ASSOC);
        }
        if($tab_email){
            echo 1 ;
        }else{
            echo 0 ;
        }
    }catch(Exception $e){
        echo $e->getMessage();
    }
}