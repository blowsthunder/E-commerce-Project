<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');
try {
	$sql = "SELECT id,nom,email,numero,is_guest FROM users";
	$stat = $db->prepare($sql);
	$stat->execute();
	$users = $stat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
	echo 'error :' . $ex;
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
					<h2>All Users</h2>
					<div class="Filter">
						<input type="text" name="searchBar" id="searchBar" placeholder='Search Products'>
					</div>
					<table>
						<tr>
							<th>Id</th>
							<th>User</th>
							<th>Email</th>
							<th>Number</th>
							<th>Action</th>
						</tr>
						<?php foreach ($users as $user) { ?>
								<tr
									style="background-color: <?= $user['is_guest'] ? 'rgba(0,0,0,0.3)' : 'rgba(0,255,0,0.3)' ?>">
									<td>
										<?= $user['id'] ?>
									</td>
									<td>
										<?= $user['nom'] ?>
									</td>
									<td>
										<?= $user['email'] ? $user['email'] : 'No email is set' ?>
									</td>
									<td>
										<?= $user['numero'] ? $user['numero'] : 'No number is set' ?>
									</td>
									<td style="display:flex;flex-direction:column">

										<a href="UserHistory.php?id=<?= $user['id']?>">View User History</a>
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