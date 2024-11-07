<?php
include_once '../includes/db_connection.php';
// Check if the user is authenticated (logged in);
if (isset($_POST['id']) && isset($_POST['action']) && isset($_POST['cart']) && isset($_POST['user_id'])) {
    $id = (int) $_POST['id']; // Cast id to an integer
    $action = $_POST['action'];
    $item = $_POST['cart'];
    $user_id_value = $_POST['user_id'];

    if ($item == 'guest_cart') {
        $column = 'session_id';
    } elseif ($item == 'cart') {
        $column = 'user_id';
    }

    function calculateTotalSum($db, $card, $column, $user_id_value) {
        $sqlTotalSum = "SELECT SUM(c.quantity * p.sale_price) AS total_price FROM $card c
        JOIN products p ON c.product_id = p.id
        WHERE c.$column = :id;";
        
        $statementSum = $db->prepare($sqlTotalSum);
        $statementSum->bindParam(':id', $user_id_value);
        $success = $statementSum->execute();
        
        if ($success) {
            return $statementSum->fetchColumn();
        } else {
            return null; // Return null or handle the error as needed
        }
    }
    
   try {
        if ($action === 'add') {
            $sql = "UPDATE $item
                    SET quantity = quantity + 1
                    WHERE id = :id";
            $statement = $db->prepare($sql);
            $statement->bindParam(':id', $id);
            if ($statement->execute()) {
                // Query to fetch the updated quantity
                $fetchSql = "SELECT quantity FROM $item WHERE id = :id";
                $fetchStatement = $db->prepare($fetchSql);
                $fetchStatement->bindParam(':id', $id);
                $fetchStatement->execute();
                $updatedQuantity = $fetchStatement->fetchColumn();
                $Sum = calculateTotalSum($db, $item, $column, $user_id_value);

                if ($Sum !== null) {
                    // Return the updated quantity and the total sum as JSON
                    echo json_encode(['quantity' => $updatedQuantity, 'sum' => $Sum]);
                } else {
                    echo json_encode(['error' => 'Could not retrieve total sum']);
                }
            } else {
                echo json_encode(['error' => 'could not update']);
            }
        } elseif ($action == 'subtract') {
            $sqlSub = "UPDATE $item
                    SET quantity = quantity - 1
                    WHERE id = :id";
            $statementSub = $db->prepare($sqlSub);
            $statementSub->bindParam(':id', $id);
            if ($statementSub->execute()) {
                // Query to fetch the updated quantity
                $fetchSql = "SELECT quantity FROM $item WHERE id = :id";
                $fetchStatement = $db->prepare($fetchSql);
                $fetchStatement->bindParam(':id', $id);
                $fetchStatement->execute();
                $updatedQuantity = $fetchStatement->fetchColumn();

                $Sum = calculateTotalSum($db, $item, $column, $user_id_value);

                if ($Sum !== null) {
                    // Return the updated quantity and the total sum as JSON
                    echo json_encode(['quantity' => $updatedQuantity, 'sum' => $Sum]);
                } else {
                    echo json_encode(['error' => 'Could not retrieve total sum']);
                }
            } else {
                echo json_encode(['error' => 'could not update']);
            }
        } elseif ($action == 'delete') {
            $sqlDel = "DELETE FROM $item WHERE id = :id;";
            $statement = $db->prepare($sqlDel);
            $statement->bindParam(':id', $id);
            if ($statement->execute()) {
                $Sum = calculateTotalSum($db, $item, $column, $user_id_value);
                echo json_encode(['quantity' => 'DELETED', 'item' => $id, 'sum' => $Sum]);
            }
        }


        

    } catch (PDOException $err) {
        echo json_encode(['error' => $err->getMessage()]);
    }

}