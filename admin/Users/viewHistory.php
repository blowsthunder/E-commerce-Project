<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');

$order_id = $_GET['id'];
try {
    $sqlItems = "SELECT i.id,
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
    $statetment = $db->prepare($sqlItems);
    $statetment->bindParam(':order_id', $order_id);
    $statetment->execute();
    $itemsOrder = $statetment->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo 'Error' . $ex;
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

                <!-- The page Content  -->
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
        </div> <!-- main-content -->
    </section> <!-- main -->

    <script src="../app.js"></script>
</body>

</html>