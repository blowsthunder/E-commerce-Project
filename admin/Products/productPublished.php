<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');
	// Retrieve product data from the database
try {
	$query = "SELECT p.id, p.name, p.regular_Price, p.sale_Price, p.description, c.name AS category_name, p.image_principale, p.image_hover, p.small_image, p.stock, p.display, p.join_date
    FROM products p
    JOIN category c ON p.category_id = c.id
    WHERE p.display = 1";
	
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    $message = 'Error adding product: ' . $ex->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
	<!-- SVG Headers -->
<?php include_once('../includes/Header.php')?>

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

		<!-- *****************The page Content*******************  -->
		<p>Manage > <a style="text-decoration:none;color:#333" href="Products.php">Products</a></p>
		<h1>Products</h1>
		<div class="products_stats">
			<a href="Products.php" >All</a>
			<a href="productPublished.php" >Visible</a>
			<a href="productDisabled.php" >Invisible</a>
			<a href="productAvailable.php" >In stock</a>
			<a href="productInAvailable.php" >Out of Stock</a>
		</div>
		<div class="Filter">
		<input type="text" name="searchBar" id="searchBar" placeholder='Search Products'>
			<select id="category" name="category">
				<option value="volvo">Category</option>
				<option value="saab">Saab</option>
				<option value="fiat">Fiat</option>
				<option value="audi">Audi</option>
			</select>
						
		</div>

		<div class="btn_add-products">
					<a href="addProduct.php" class="btn_add-products">
						<button>Add Product</button>
					</a>
					<a href="../categorie/categorie.php" class="btn_add-products">
						<button>View Categories</button>
					</a>
				</div>
		
		<h1>You Have <?= count($products) ?> items</h1>
		
		<section class="user-list">
					<table>
				<tr>
					<th>
						<input type="checkbox" name="" id="">
					</th>
					<th></th>
					<th>Products Name</th>
					<th>Price</th>
					<th>Category</th>
					<th>Stock</th>
					<th>Status</th>
					<th>Published On</th>
					<th></th>
				</tr>
				
				<?php foreach ($products as $product) { ?>
					<tr style="background-color: <?= 
						$product['display'] === 0
							? 'rgba(0, 0, 0, 0.1)'
							: ($product['stock'] > 0 ? 'rgba(0, 255, 0, 0.25)' : 'rgba(255, 0, 0, 0.25)') 
						?>;
						opacity: <?= $product['display'] === 0 ? '0.6' : '1.0' ?>;">

					<td>
						<input type="checkbox" name="" id="">
					</td>
					<td>
						<img style="width: 60px" src="<?= $product['image_principale'] ?>" alt="">
					</td>
					<td><?= $product['name'] ?></td>
					<td><?= $product['sale_Price'] ?></td>
					<td><?= $product['category_name']?></td>
					<td style="color: <?= $product['stock'] > 0 ? 'green' : 'red' ?>;">
						<?= $product['stock'] > 0 ? 'Available' : 'Expired' ?> ( <?= $product['stock']?> )
					</td>
					<td>
						<?= $product['display'] === 0  ? 'Draft' : 'Published'  ?> 
					</td>
					<td><?= $product['join_date'] ?></td>
					<td>
						<a style="text-decoration:none; color:#333" href="viewProduct.php?id=<?= $product['id'] ?>"><i class="fa-solid fa-eye"></i></a>
						<a href="confirm_deleteOrActivate.php?id=<?= $product['id'] ?>"> <?= $product['display'] === 0  ? 'Activate' : 'Desactivate'  ?> </a>
					</td>
				</tr>
				<?php } ?>
				
			</table>

   		</section>

		</div> <!-- main-content -->
		</section> <!-- main -->
	
	<script src="CostumApp.js"></script>
	<script src="../app.js"></script>
</body>
</html>