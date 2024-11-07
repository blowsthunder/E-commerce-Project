<?php
include_once('../../includes/sessionCheck.php');
include_once('../../includes/db_connection.php');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    try {
        $SQLquery = "SELECT * FROM order_tracking WHERE order_id = :order_id";
        $statmCheck = $db->prepare($SQLquery);
        $statmCheck->bindParam(':order_id', $order_id);
        $statmCheck->execute();
        $order_tracking = $statmCheck->fetch(PDO::FETCH_ASSOC);
        echo var_dump($order_tracking);

        if($order_tracking['isShipped'] === 0){
            $sql = 'UPDATE order_tracking set isShipped= :isShipped WHERE order_id = :order_id';
            $stat = $db->prepare($sql);
            $stat->bindParam(':order_id', $order_id);
            $yes=1;
            $stat->bindParam(':isShipped',$yes);
            $stat->execute();
            echo 'Product is ready to be Shipped';
        }else{
            echo '<button type="button" id="setDeliveryButton">SetDelivery</button>';
        }


    } catch (PDOException $ex) {
        error_log('Database error: ' . $ex->getMessage());
        echo 'An error occurred while processing your request. Please try again later.';
    }
}