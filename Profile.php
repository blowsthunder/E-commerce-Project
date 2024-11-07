<?php
session_start();
//includes
include_once('includes/db_connection.php');

if (isset($_SESSION['id']) && isset($_SESSION['Connected']) && $_SESSION['Connected'] === 1) {
    $id = (int) $_SESSION['id'];
    try {
        $sql = 'SELECT nom,numero,email FROM users WHERE id = :id';
        $statement = $db->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $userInfo = $statement->fetch(PDO::FETCH_ASSOC);

        $sqlItems = 'SELECT id,order_date FROM orders WHERE user_id = :id';
        $statement = $db->prepare($sqlItems);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $orders = $statement->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll() to get all orders

        if ($orders) {
            $order_items = [];
            foreach ($orders as $order) {
                $order_id = $order['id']; // Assuming 'id' is the column name for the order_id in the orders table
                $date = $order['order_date'];

                $sql_Items = "SELECT p.sale_Price, p.image_principale AS image, o.quantity, o.subtotal, orders.order_date AS date, orders.status_id AS status_id,
                t.delivering_date, t.estimated_delivery_date, t.tracking_number
         FROM order_items o
         JOIN orders ON o.order_id = orders.id
         JOIN products p ON o.product_id = p.id
         JOIN order_tracking t ON o.order_id = t.order_id
         WHERE o.order_id = :order_id
         ";
                $secondStatement = $db->prepare($sql_Items);
                $secondStatement->bindParam(":order_id", $order_id, PDO::PARAM_INT); 
                $secondStatement->execute();
                $fetched_order_items = $secondStatement->fetchAll(PDO::FETCH_ASSOC);    
                $order_items = array_merge($order_items, $fetched_order_items);
            }
        } else {
            echo "No orders found for the given user."; // Handle case where no orders are found
        }

    } catch (Exception $e) {
        E_CORE_WARNING;
        echo $e->getMessage();
    }
} else {
    header('Location: index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style_users.css?t=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/navigation.css?t=<?= time() ?>">
    <title>User Profile</title>
</head>
<style>
    .navbar {
        position: fixed;
        background-color: white
    }

    .navbar .navbar_logo img {
        filter: none;
    }

    .navbar ul li a {
        color: black;
    }

    .navbar .navbar_icons i {
        color: black;
    }
</style>
<?php include_once('includes/navigation.php') ?>

<body>
    <div class="container_user">


        <div class="pannel">
            <div class="sidebar">
                <div class="menu_profile">
                    <h1>User Menu</h1>
                    <ul>
                        <li><a href="#profile" onclick="showSection('profile')" class="section_active"
                                id="menu_profile">Profile</a></li>
                        <li><a href="#orders" onclick="showSection('orders')" id="menu_orders">Order History</a></li>
                        <li><a href="#tracking" onclick="showSection('tracking')" id="menu_tracking">Tracking Orders</a>
                        </li>
                        <!-- Add more links for different sections if needed -->
                    </ul>
                </div>
            </div>
            <div class="main-content">
                <div id="profile">
                    <h1>User Profile</h1>
                    <!-- Add profile details here -->
                    <p>Profile details go here...</p>

                    <div class="info_Item">
                        <h4>Name</h4>
                        <div class="info">
                            <p>
                                <?= $userInfo['nom'] ?>
                            </p>
                        </div>
                    </div>

                    <div class="info_Item">
                        <h4>email</h4>
                        <div class="info">
                            <p>
                                <?= $userInfo['email'] ?>
                            </p>
                        </div>
                    </div>

                    <div class="info_Item">
                        <h4>number</h4>
                        <div class="info">
                            <p>
                                <?= $userInfo['numero'] ?>
                            </p>
                        </div>
                    </div>

                </div>



                <div id="orders" style="display: none">
                    <h1>Order History</h1>
                    <!-- Add order history details here -->
                    <p>Order history goes here...</p>
                    <section class="user-list">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Price per Unit</th>
                                    <th>Quantity</th>
                                    <th>Total Price</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item) {
                                    if ($item['status_id'] === 4) {
                                        ?>
                                        <tr class="order-item">
                                            <td class="item-image">
                                                <img src="<?= $item['image'] ?>" alt="Product Image">
                                            </td>
                                            <td class="item-price">
                                                <?= $item['sale_Price'] ?>
                                            </td>
                                            <td class="item-quantity">
                                                <?= $item['quantity'] ?>
                                            </td>
                                            <td class="item-subtotal">
                                                <?= $item['subtotal'] ?>
                                            </td>
                                            <td class="item-date">
                                                <?= $item['delivering_date'] ?>
                                            </td>
                                            <td class="item-date">
                                                Delivered
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } ?>
                            </tbody>
                        </table>
                    </section>
                </div>


                <!-- Add more sections for different content -->
                <div id="tracking" style="display: none">
                    <h1>Tracking your orders</h1>
                    <!-- Add order history details here -->
                    <p>Order tracking goes here...</p>
                    <section class="user-list">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Price per Unit</th>
                                    <th>Quantity</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Estimated Time to deliver</th>
                                    <th>Tracking Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item) {
                                    if ($item['status_id'] != 4 && $item['status_id'] != 5) {
                                        ?>
                                        <tr class="order-item">
                                            <td class="item-image">
                                                <img src="<?= $item['image'] ?>" alt="Product Image">
                                            </td>
                                            <td class="item-price">
                                                <?= $item['sale_Price'] ?>
                                            </td>
                                            <td class="item-quantity">
                                                <?= $item['quantity'] ?>
                                            </td>
                                            <td class="item-subtotal">
                                                <?= $item['subtotal'] ?>
                                            </td>
                                            <td>
                                                <?php
                                                switch ($item['status_id']) {
                                                    case 1:
                                                    echo 'Pending';
                                                    break;
                                                    case 2:
                                                    echo 'Shipping';
                                                    break;
                                                    case 3:
                                                    echo 'delivering';
                                                    break;
                                                }
                                                ?>
                                            </td>
                                            <td class="item-date">
                                                <?= $item['estimated_delivery_date'] ? $item['estimated_delivery_date'] : 'Your item is still in the Process' ?>
                                            </td>
                                            <td class="item-date">
                                                <?= $item['tracking_number'] ? $item['tracking_number'] : 'Not set Yet !' ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } ?>
                            </tbody>
                        </table>
                    </section>
                </div>


            </div>
        </div>
    </div>
    <script>
        function showSection(sectionId) {
            // Hide all sections
            const sections = document.querySelectorAll('.main-content > div');
            const sectionsMenu = document.querySelectorAll('.menu_profile ul li a');
            sections.forEach(section => {
                section.style.display = 'none';
            });
            console.log(sections)
            console.log(sectionsMenu);
            sectionsMenu.forEach(sectionMenu => {
                sectionMenu.classList.remove("section_active");
            })

            // Show the selected section
            const selectedSection = document.getElementById(sectionId);
            const selectedMenu = document.getElementById('menu_' + sectionId);
            if (selectedSection) {
                selectedSection.style.display = 'block';
                selectedMenu.classList.add("section_active");
            }
        }
    </script>
</body>
<script src="app.js?t=<?= time() ?>"></script>

</html>