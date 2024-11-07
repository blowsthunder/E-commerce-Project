<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');
// Retrieve product data from the database
$order_id = $_GET['id'];
$username = $_GET['user'];
echo 'Hello';

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

if (isset($_POST['addquantity'])) {
    $newQuantity = $_POST['newQuantity'];
    $product_id = $_POST['product_id'];
    $product_sale_price = $_POST['product_Price'];
    $subprice = $newQuantity * $product_sale_price;
    try {
        $sql = "UPDATE `order_items` 
            SET `quantity` = :newquantity, `subtotal` = :subprice 
            WHERE order_id = :order_id AND product_id = :product_id";

        $statement = $db->prepare($sql);
        $statement->bindParam(':newquantity', $newQuantity);
        $statement->bindParam(':subprice', $subprice);
        $statement->bindParam(':product_id', $product_id);
        $statement->bindParam(':order_id', $order_id);
        echo $statement->queryString;
        if ($statement->execute()) {
            // Construct the URL with additional parameters
            $redirectUrl = 'viewOrder.php?id=' . $order_id . '&user=' . $username;
            header('Location: ' . $redirectUrl);
        } else {
            echo 'Failed to update quantity.';
            // You could also log the error details here
        }
    } catch (PDOException $ex) {
        echo 'Error has been produced ' . $ex;
    }
}

if(isset($_POST['deleteitems'])){
    $product_id = $_POST['product_id'];
try {
    $sql = "DELETE FROM `order_items` WHERE order_id = :order_id AND product_id = :product_id";
    $statement = $db->prepare($sql);
    $statement->bindParam(':order_id', $order_id);
    $statement->bindParam(':product_id', $product_id);

    if ($statement->execute()) {
        header('location:viewOrder.php');
    } else {
        echo 'Failed to delete item.';
        // You could also log the error details here
    }
} catch (PDOException $ex) {
    echo 'An error occurred: ' . $ex->getMessage();
    // You could also log the error details here
}



}

if(isset($_POST['confirmProduct'])){
    try{
        $sql="UPDATE `orders` SET status_id = '2' WHERE id= :id";
        $statement = $db->prepare($sql);
        $statement->bindParam(':id',$order_id);
        if($statement->execute()){
            Header('location:Orders.php');
        }else{
            echo 'Error ';
        }
    } catch (PDOException $ex) {
        echo 'An error occurred: ' . $ex->getMessage();
        // You could also log the error details here
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
                <form method="post">
                    <!-- The page Content  -->
                    <div class="OrderInformation">
                        <H1>ID order :
                            <?= $order_id ?>
                        </H1>
                        <h3>Client name :
                            <?= $username ?>
                        </h3>
                    </div>

                    <div class="allproducts">
                        <?php foreach ($itemsOrder as $items) { ?>
                            <div class="productItem">
                                <input type="hidden" name="product_id" value="<?= $items['product_id'] ?>">
                                <input type="hidden" name="product_Price" value="<?= $items['product_sale_price'] ?>">
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

                                <div class="action">
                                    <input type="number" name="newQuantity" value="<?= $items['quantity'] ?>">
                                    <input type="submit" value="Add Quantity" name="addquantity" />
                                    <input type="submit" value="Delete items"  name="deleteitems"/>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <input type="submit" value="Confirm The product" name="confirmProduct" id="confirmProduct">
            </div>
            </form>
        </div>
        </div> <!-- main-content -->
    </section> <!-- main -->

    <script src="../app.js"></script>
    <script>

    </script>
</body>

</html>