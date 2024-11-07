<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');
try {
	$sqlItems = "SELECT * FROM category";
	$statetment = $db->prepare($sqlItems);
	$statetment->execute();
	$categorys = $statetment->fetchAll(PDO::FETCH_ASSOC);
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
				<section class="user-list">
					<h2>Categories</h2>
					<div class="btn_add-products">
					<a href="addCategory.php" class="btn_add-products">
						<button>Add Categorie</button>
					</a>
				</div>
					<table>
						<tr>
							<th></th>
							<th>Name</th>
							<th>Display Image</th>
							<th>Description</th>
							<th>Action</th>
						</tr>
						<?php foreach ($categorys as $category) { ?>
							<tr>
								<td>
									<img src="<?= $category['image'] ?>">
								</td>
								<td>
									<?= $category['name'] ?>
								</td>
								<td>
									<img src="<?= $category['image_display'] ?>">
								</td>
								<td>
									<?= $category['description'] ?>
								</td>
								<td style="display:flex;flex-direction:column">
									<a href="updateCategory.php?id=<?= $category['id'] ?>">Update Categorie</a>
								</td>
							</tr>
						<?php } ?>

					</table>
				</section>

			</div>
		</div> <!-- main-content -->
	</section> <!-- main -->

	<script src="app.js"></script>
</body>

</html>