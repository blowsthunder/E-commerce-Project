<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');
// Retrieve product data from the 

$display = isset($_GET['display']) ? $_GET['display'] : null;
$stock = isset($_GET['stock']) ? $_GET['stock'] : null;

try {
    $query = "SELECT p.id, p.name, p.regular_Price, p.sale_Price, p.description, c.name AS category_name, p.image_principale, p.image_hover, p.small_image, p.stock, p.display, p.join_date
    FROM products p
    JOIN category c ON p.category_id = c.id
    WHERE 1"; // Start with a TRUE condition

    if (!is_null($display)) {
        if ($display === '1') {
            $query .= " AND p.display = 1"; // Add condition for display
        } elseif ($display === '0') {
            $query .= " AND p.display = 0"; // Add condition for display
        }
    }

    if (!is_null($stock)) {
        if ($stock === '0') {
            $query .= " AND p.stock = 0"; // Add condition for stock
        } elseif ($stock > '0') {
            $query .= " AND p.stock >= 1"; // Add condition for stock
        }
    }

    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo 'Error adding product: ' . $ex->getMessage();
}







try {
	$sql = "SELECT count(*) AS total,
		SUM(CASE WHEN display = 1 THEN 1 ELSE 0 END) AS visible_count,
		SUM(CASE WHEN display = 0 THEN 1 ELSE 0 END) AS Invisible_count,
		SUM(CASE WHEN stock > 0 THEN 1 ELSE 0 END) AS instock_count,
		SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) AS outofstock_count
		FROM products";
	$statement = $db->prepare($sql);
	$statement->execute();
	$counts = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
	echo 'Error adding product: ' . $ex->getMessage();
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

				<div id="blur" class="blur"></div>
				<div id="responseDiv">
					<div id="displayInfo" class="displayInfo">
						<i id="CloseTab" style="color:red;" class="fa-solid fa-circle-xmark"></i>
						<div class="images">
							<img src="">
							<img src="">
							<img src="">
							<img src="">
						</div>
						<div class="information">
							<h1>Product Info</h1>
							<h4>Product name</h4>
							<h3>product price</h3>
							<h3>stock</h3>
							<p>product description</p>
						</div>
					</div>
				</div>

				<!-- *****************The page Content*******************  -->
				<p>Manage > <a style="text-decoration:none;color:#333" href="Products.php">Products</a></p>
				<h1>Products</h1>
				<div class="products_stats">
					<a href="Products.php">All (
						<?= $counts[0]['total'] ?>)
					</a>
					<a href="Products.php?display=1">Visible (
						<?= $counts[0]['visible_count'] ?>)
					</a>
					<a href="Products.php?display=0">Invisible (
						<?= $counts[0]['Invisible_count'] ?>)
					</a>
					<a href="Products.php?stock=1">In stock (
						<?= $counts[0]['instock_count'] ?>)
					</a>
					<a href="Products.php?stock=0">Out of Stock (
						<?= $counts[0]['outofstock_count'] ?>)
					</a>
				</div>
				<div class="Filter">
					<input type="text" name="searchBar" id="searchBar" placeholder='Search Products'>
				</div>

				<div class="btn_add-products">
					<a href="addProduct.php" class="btn_add-products">
						<button>Add Product</button>
					</a>
					<a href="../categorie/categorie.php" class="btn_add-products">
						<button>View Categories</button>
					</a>
				</div>

				<h1>You Have
					<?= count($products) ?> items
				</h1>

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
						opacity: <?= $product['display'] === 0 ? '0.6' : '0.99' ?>;">

								<td>
									<input type="checkbox" name="" id="">
								</td>
								<td>
									<img style="width: 60px" src="../../getImageAPI.php?id=<?= $product['id']?>&image_type=image_principale" alt="">
								</td>
								<td>
									<?= $product['name'] ?>
								</td>
								<td>
									<?= $product['sale_Price'] ?>
								</td>
								<td>
									<?= $product['category_name'] ?>
								</td>
								<td style="color: <?= $product['stock'] > 0 ? 'green' : 'red' ?>;">
									<?= $product['stock'] > 0 ? 'Available' : 'Expired' ?> (
									<?= $product['stock'] ?> )
								</td>
								<td>
									<?= $product['display'] === 0 ? 'Draft' : 'Published' ?>
								</td>
								<td>
									<?= $product['join_date'] ?>
								</td>
								<td>
									<i class="fa-solid fa-eye displayButton" data-id="<?= $product['id'] ?>"></i>
									<a style="text-decoration:none; color:#333"
										href="viewProduct.php?id=<?= $product['id'] ?>"><i
											class="fa-solid fa-pen-to-square"></i></a>
									<a href="confirm_deleteOrActivate.php?id=<?= $product['id'] ?>">
										<?= $product['display'] === 0 ? '<i style="color:red" class="fa-regular fa-hand"></i>' : '<i style="color:green" class="fa-solid fa-check"></i>' ?>
									</a>

								</td>
							</tr>
						<?php } ?>

					</table>

				</section>

			</div> <!-- main-content -->
	</section> <!-- main -->


	<script>
		document.addEventListener("DOMContentLoaded", function () {
			// Select all elements with the class "displayButton"
			var displayButtons = document.querySelectorAll('.displayButton');
			var blur = document.getElementById('blur');
			var displayInfo = document.getElementById('displayInfo');

			// Iterate over each displayButton element and attach a click event listener
			displayButtons.forEach(function (displayButton) {
				displayButton.addEventListener('click', function () {
					var blur = document.getElementById('blur');
					var responseDiv = document.getElementById('responseDiv');
					var productId = this.getAttribute('data-id');

					var xhr = new XMLHttpRequest;
					xhr.open('POST', 'sendInfoProduct.php', true);
					xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
					xhr.onreadystatechange = function () {
						if (xhr.readyState === 4 && xhr.status === 200) {
							// Handle the response from sendInfoProduct.php if needed
							var response = xhr.responseText;
							responseDiv.innerHTML = response;
							blur.classList.add('blurActive');
							displayInfo.classList.add('displayInfoActive');
						}
					};

					// Send the productId as POST data
					xhr.send('id=' + productId);
				});
			});

			var closeIcon = document.getElementById('closeIcon');

			closeIcon; addEventListener('click', () => {
				var displayInfoActive = document.getElementById('displayInfoActive');
				var blurActive = document.getElementById('blur');
				displayInfoActive.classList.remove('displayInfoActive');
				displayInfoActive.classList.add('displayInfo');
				blurActive.classList.remove('blurActive');
			})

		});

	</script>

	<script src="CostumApp.js"></script>
	<script src="../app.js"></script>
</body>

</html>