<?php
    if(count($_POST)>0){
        foreach ($_POST as $key=>$value){
            ${$key}=$value;
        }
    }
    if(count($_GET)>0){
        foreach ($_GET as $key=>$value){
            ${$key}=$value;
        }
    }
?>
