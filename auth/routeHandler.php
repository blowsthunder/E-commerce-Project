<?php
session_start();
if(isset($_SESSION['id'])){
    $sessionId = (int)trim($_SESSION['id']);
    if($sessionId === 1){
        header("Location: ../admin/Index/index.php");
    }else{
        header("Location: ../profile.php");
    }
}else{
  header("Location: login.php");
}
?>