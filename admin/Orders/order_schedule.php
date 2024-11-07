<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');
// Retrieve product data from the database
$order_id = $_GET['id'];
$userid = $_GET['user_id'];

$message = '';
try {
    $sql = 'SELECT city,street FROM orders WHERE id = :id';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $order_id);
    $stmt->execute();
    $orderInfo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo 'err' . $ex;
}


try {
    $sql = 'SELECT nom,numero FROM users WHERE id = :id';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $userid);
    $stmt->execute();
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo 'err' . $ex;
}

try {
    $sql = "SELECT i.id,
    i.order_id,
    i.product_id,
    p.id,
    p.image_principale as image,
    p.name AS product_name,
    p.sale_Price AS product_sale_price,
    i.quantity,
    i.subtotal
FROM order_items i
INNER JOIN products p ON i.product_id = p.id
WHERE i.order_id = :order_id";

    $statement = $db->prepare($sql);
    $statement->bindParam(':order_id', $order_id);
    $statement->execute();
    $itemsOrder = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo 'Error has been produced ' . $ex;
}


if (isset($_POST['finish'])) {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $courrier = $_POST['courrier'];
    $trackingNumber = $_POST['tracking_number'];

    // Check if any of the required fields are empty
    if (empty($date) || empty($time) || empty($courrier) || empty($trackingNumber)) {
        $message = "All fields are required.";
    } else {
        try {
            // Assuming you have a database connection named $db

            // Concatenate date and time to create the complete date-time value
            $dateComplet = $date . ' ' . $time;

            // Update the information in the database
            $sql = "UPDATE order_tracking SET estimated_delivery_date = :estimated_delivery_date, courrier = :courrier, tracking_number = :tracking_number WHERE order_id = :order_id";
            $stmt = $db->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':estimated_delivery_date', $dateComplet);
            $stmt->bindParam(':courrier', $courrier);
            $stmt->bindParam(':tracking_number', $trackingNumber);
            $stmt->bindParam(':order_id', $order_id);

            // Execute the query
            if ($stmt->execute()) {
            } else {
                echo "Error updating data in the database.";
            }

            // Update the status_id in the orders table
            $sql = "UPDATE orders SET status_id = :status_id WHERE id = :order_id"; // Corrected the SQL statement
            $stmt = $db->prepare($sql);
            $status_id = 3; // Assuming 3 is the status_id for 'Delivered'
            $stmt->bindParam(':status_id', $status_id); // Corrected the parameter name
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
        } catch (PDOException $ex) {
            echo 'Database error: ' . $ex->getMessage();
        }
    }
}

if (isset($_POST['Delivered'])) { // Correct the form field name to 'Delivered'
    try {
        // Update the delivering_date in the order_tracking table
        $sql = "UPDATE order_tracking SET delivering_date = NOW() WHERE order_id = :order_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);

        // Execute the query
        if ($stmt->execute()) {
        } else {
            $message = "Error updating data in the database.";
        }

        // Update the status_id in the orders table
        $sql = "UPDATE orders SET status_id = :status_id WHERE id = :order_id"; // Corrected the SQL statement
        $stmt = $db->prepare($sql);
        $status_id = 4; // Assuming 3 is the status_id for 'Delivered'
        $stmt->bindParam(':status_id', $status_id); // Corrected the parameter name
        $stmt->bindParam(':order_id', $order_id);

        // Execute the query
        if ($stmt->execute()) {
        } else {
            $message = "Error updating order status in the database.";
        }
    } catch (PDOException $ex) {
        echo 'Database error: ' . $ex->getMessage();
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<!-- SVG Headers -->
<?php include_once('../includes/Header.php') ?>

<body>
    <!-- SVG Definitions -->
    <?php include_once("../includes/SVGdefinition.php") ?>

    <!-- Include Navigation bar -->
    <?php include_once("../includes/navigation.php") ?>

    <section id="main">
        <div id="overlay"></div>

        <!-- Include Side bar -->
        <?php include_once("../includes/sideBar.php") ?>

        <div id="main-content">
            <div id="main-content__container">
                <!-- Order Shipping and Delivery Management Code -->
                <section class="order-details">
                    <!-- The page Content  -->
                    <div class="OrderInformation">
                        <div class="header">
                            <h1>Order Information</h1>
                        </div>
                        <div class="Information">
                            <H1>ID order :
                                <?= $order_id ?>
                            </H1>
                            <h3>Client name :
                                <?= $userInfo['nom'] ?>
                            </h3>
                            <h3>Client number :
                                <?= $userInfo['numero'] ?>
                            </h3>
                            <h3>Order city :
                                <?= $orderInfo['city'] ?>
                            </h3>
                            <h3>Order street :
                                <?= $orderInfo['street'] ?>
                            </h3>
                        </div>
                    </div>

                    <div class="allproducts">
                        <?php foreach ($itemsOrder as $items) { ?>
                            <div class="productItem">
                                <div class="productImage">
                                    <img src="../../getImageAPI.php?id=<?= $items['id']?>&image_type=image_principale" alt="">
                                </div>
                                <div class="productInfo">
                                    <h1>Product Information</h1>
                                    <h4>Product name :
                                        <?= $items['product_name'] ?>
                                    </h4>
                                    <h4>product sale :
                                        <?= $items['product_sale_price'] ?>
                                    </h4>
                                    <h4>
                                        Product Quantity :
                                        <?= $items['quantity'] ?>
                                    </h4>
                                    <h4>Product subtotal :
                                        <?= $items['subtotal'] ?>
                                    </h4>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

            </div>


            <section class="shipping-form">
                <form method="post">
                    <div id="Response">
                        <!-- Here -->

                    </div>
                </form>
            </section>

            <?php if (!empty($message)) { ?>
                <div id="erreur">
                    <?= $message ?>
                </div>
            <?php } ?>

            <section class="delivery-form" style="display: none;">
                <h2>Set Delivery Information</h2>
                <form id="delivery" method="POST">
                    <!-- Delivery input fields (e.g., delivery date) -->
                    <label for="delivery_date">Delivery Date:</label>
                    <input type="date" name="date" />
                    <input type="time" id="time" name="time">
                    <input type="text" placeholder="courrier" name="courrier" />
                    <input type="number" placeholder="Tracking Number" name="tracking_number" />

                    <button name="finish" type="submit">Set Delivery</button>
                </form>
            </section>

            <!-- End of Order Shipping and Delivery Management Code -->
        </div>
        </div> <!-- main-content -->
    </section> <!-- main -->

    <script src="app.js"></script>
    <script>
        window.onload = isShipped;

        function isShipped() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'API/isShipped.php?order_id=' + <?= $order_id ?>, true);
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('Response').innerHTML = xhr.response;
                }
            };
            xhr.send();
        }


        // Check if the button element exists by its ID
        document.getElementById('Response').addEventListener('click', () => {
            function confirmShipping() {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'API/isShipped.php?order_id=<?= $order_id ?>&Shipped=1', true);
                xhr.onreadystatechange = () => {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        document.getElementById('Response').innerHTML = xhr.response;
                    }
                };
                xhr.send();
                console.log(xhr);
            }

            function displayDeliveryTime() {
                var deliveryForm = document.querySelector('.delivery-form');
                var response = document.getElementById('Response');
                response.style.display = 'none';
                deliveryForm.style.display = 'block'
            }
            if (event.target.id === 'isShippedButton') {
                console.log('button clicked');
                confirmShipping();
            }

            if (event.target.id === 'setDeliveryButton') {
                console.log('button clicked');
                displayDeliveryTime();
            }

        })




    </script>

</body>

</html>