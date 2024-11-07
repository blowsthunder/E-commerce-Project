<?php
include_once('../../includes/sessionCheck.php');
include_once('../../includes/db_connection.php');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    try {
        // Query to retrieve the status_id
        $query = "SELECT status_id FROM orders WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindParam(':id', $order_id);
        $statement->execute();
        $status_id = $statement->fetch(PDO::FETCH_ASSOC);

        $SQLquery = "SELECT * FROM order_tracking WHERE order_id = :order_id";
        $statmCheck = $db->prepare($SQLquery);
        $statmCheck->bindParam(':order_id', $order_id);
        $statmCheck->execute();
        $order_tracking = $statmCheck->fetch(PDO::FETCH_ASSOC);

        if(isset($_GET['Shipped'])){
            $Shipped = $_GET['Shipped'];
        }

        if (empty($order_tracking)) {
            // Insert into order_tracking
            $sql = "INSERT INTO order_tracking (order_id, isShipped, created_at) VALUES (?, ?, NOW())";
            $stat = $db->prepare($sql);
            $stat->bindParam(1, $order_id);
            $stat->bindParam(2, $NO);
            $stat->execute();
            echo "<p>Orders tracking Created !</p>";
            echo '<button type="button" id="isShippedButton">Is The Order Shipping ?</button>';
        }elseif($order_tracking['isShipped']==0 && !isset($Shipped)){
            echo '<button type="button" id="isShippedButton">Is The Order Shipping?</button>';
        }elseif($order_tracking['isShipped']==0 && isset($Shipped) && $Shipped === '1'){
            $sql = 'UPDATE order_tracking set isShipped= :isShipped WHERE order_id = :order_id';
            $stat = $db->prepare($sql);
            $stat->bindParam(':order_id', $order_id);
            $yes=1;
            $stat->bindParam(':isShipped',$yes,PDO::PARAM_BOOL);
            if($stat->execute()){
                echo '<p>Product is Shipping</p>';
                echo '<button type="button" id="setDeliveryButton">Set Time to Delivere</button>';
            }else{
                echo 'Couldnt Ship the product';
            }
            
        }elseif($order_tracking['isShipped']===1 && empty($order_tracking['estimated_delivery_date'])){
            echo '<p style="color:green">The Product is already Shipped , Set a estimated Time for the client to deliver : </p></br>';
            echo '<button type="button" id="setDeliveryButton">Set Time to Delivere</button>';
        }elseif($order_tracking['isShipped']===1 && isset($order_tracking['estimated_delivery_date'])  && !isset($order_tracking['delivering_date'])){
            echo '<p>Product Time Delivery is already Set on '. $order_tracking['estimated_delivery_date'].'</p>';
            echo '<p>if the product is delivered with no problem Click The button</p>';
            echo '<button type="submit" name="Delivered"> Delivered !</button>';
        }elseif(isset($order_tracking['delivering_date'])){
            echo '<p>The Order Has been delivered In This date '.$order_tracking['delivering_date'] .'</p>';
        }


    } catch (PDOException $ex) {
        error_log('Database error: ' . $ex->getMessage());
        echo 'An error occurred while processing your request. Please try again later.';
    }
}
?>