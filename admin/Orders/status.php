<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');

try {
	$SQLquery = "SELECT * FROM order_status";
	$statement = $db->prepare($SQLquery);
	$statement->execute();
	$AllStatus = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $err) {
	echo 'err' . $err;
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

				<section class="user-list">
					<h2>Most Interactive Users</h2>
					<table>
						<tr>
							<th>Id</th>
							<th>Status Name</th>
						</tr>
						<?php foreach ($AllStatus as $status) { ?>
							<tr>
								<td>
									<?= $status['id'] ?>
								</td>
								<td>
									<?= $status['status'] ?>
								</td>
							</tr>
						<?php } ?>

					</table>
				</section>

			</div>
		</div> <!-- main-content -->
	</section> <!-- main -->

	<script src="../app.js"></script>
</body>

</html>