<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $productId = $_GET['id'];
    
    // Retrieve the product details for confirmation message
    try {
        $query = "SELECT name, display FROM products WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            // Handle invalid product ID
            header("Location: Products.php");
            exit();
        }
    } catch (PDOException $ex) {
        // Handle error as needed
        header("Location: Products.php");
        exit();
    
    }
}
elseif($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])){
    $productId = $_POST['product_id'];

    // Retrieve the product details from the database
    $query = "SELECT display FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: Products.php");
        exit();
    }

    // Toggle the "display" column value between 0 and 1
    $newDisplayValue = ($product['display'] === 0) ? 1 : 0;

    try {
        $updateQuery = "UPDATE products SET display = :newDisplayValue WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':newDisplayValue', $newDisplayValue, PDO::PARAM_INT);
        $updateStmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $updateStmt->execute();

        $_SESSION['success_message'] = 'Product status updated successfully';
        header("Location: Products.php");
        exit();
    } catch (PDOException $ex) {
        $_SESSION['error_message'] = 'Error updating product status';
        header("Location: Products.php");
        exit();
    }

} else {
    // Invalid request
    header("Location: Products.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<?php include_once('../includes/Header.php')?>
<body>

	<!-- SVG Definitions -->
	<?php include_once('../includes/SVGDefinition.php')?>

	<!-- main-header -->
	<?php include_once('../includes/navigation.php')?>

	<section id="main">
		<div id="overlay"></div>
		 <!-- sidebar -->
		 <?php include_once('../includes/sideBar.php')?>

		<div id="main-content">
			<div id="main-content__container">
                <div class="blur blurActive"></div>
                <div class="DeleteConfirmation">
                    <h1>Confirm <?= $product['display'] === 1 ? 'Desactivation' : 'Activation'  ?></h1>
                        <p>Are you sure you want to <?= $product['display'] === 1 ? 'Desactivate' : 'Activate' ?> the product: <?= $product['name'] ?>?</p>
                        <form  method="post">
                            <input type="hidden" name="product_id" value="<?= $productId ?>">
                            <button type="submit" name="delete_product">Yes, I'm sure</button>
                            <a href="Products.php">No, return to products</a>
                        </form>

			    </div>
            </div>
		</div> <!-- main-content -->
	</section> <!-- main -->

	<script src="app.js"></script>
</body>
</html>






























