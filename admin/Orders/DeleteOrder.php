<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');

//Get id
$id_order = $_GET['id'];

if (isset($_POST['delete_order'])) {
    try {
        $sql = 'UPDATE orders set status_id=5 WHERE id = :order_id';
        $stat = $db->prepare($sql);
        $stat->bindParam(':order_id', $id_order);

        if ($stat->execute()) {
            header('location:Orders.php');
        } else {
            echo 'Error deleting order';
        }

    } catch (PDOException $err) {
        echo 'Error: ' . $err->getMessage();
    }
}




?>


<!DOCTYPE html>
<html lang="en">
<?php include_once('../includes/Header.php') ?>

<body>

    <!-- SVG Definitions -->
    <?php include_once('../includes/SVGDefinition.php') ?>

    <!-- main-header -->
    <?php include_once('../includes/navigation.php') ?>

    <section id="main">
        <div id="overlay"></div>
        <!-- sidebar -->
        <?php include_once('../includes/sideBar.php') ?>

        <div id="main-content">
            <div id="main-content__container">
                <div class="blur"></div>
                <div class="DeleteConfirmation">
                    <h1>Confirm </h1>
                    <p>Are you sure you want to delete The order
                        <?= $id_order ?> ?
                    </p>
                    <form method="post">
                        <!-- <input type="hidden" name="product_id" > -->
                        <button type="submit" name="delete_order">Yes, I'm sure</button>
                        <a href="Orders.php">No, return to Orders</a>
                    </form>

                </div>
            </div>
        </div> <!-- main-content -->
    </section> <!-- main -->

    <script src="app.js"></script>
</body>

</html>