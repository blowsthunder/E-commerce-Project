<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');
// Retrieve product data from the database
$status = isset($_GET['status']) ? $_GET['status'] : null;
try {
	$query = "SELECT o.id,u.nom AS user_name,u.numero AS numero,o.user_id,o.status_id,o.order_date,o.total_amount,o.city,o.street,s.status AS status  
	From orders o
	JOIN users u ON o.user_id = u.id
	JOIN order_status s ON o.status_id = s.id
	";
	if (!is_null($status)) {
		$query .= " WHERE status_id = :status";
	}

	$stmt = $db->prepare($query);

	if (!is_null($status)) {
		$stmt->bindParam(':status', $status);
	}

	$stmt->execute();
	$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
	echo 'Error adding product: ' . $ex->getMessage();
}


try {
	$SQLquery = "SELECT * FROM order_status";
	$statement = $db->prepare($SQLquery);
	$statement->execute();
	$AllStatus = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $err) {
	echo 'err' . $err;
}


//count Info : 
try {
	$SQLquery = "
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) AS pending_count,
            SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) AS processing_count,
            SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) AS shipped_count,
            SUM(CASE WHEN status_id = 4 THEN 1 ELSE 0 END) AS delivered_count,
			SUM(CASE WHEN status_id = 5 THEN 1 ELSE 0 END) AS canceled_count
        FROM orders
    ";

	$statement = $db->prepare($SQLquery);
	$statement->execute();
	$counts = $statement->fetch(PDO::FETCH_ASSOC);

	// Now, you have the counts for each status in the $counts associative array.
	$countAll = $counts['total'];
	$countPend = $counts['pending_count'];
	$countProc = $counts['processing_count'];
	$countShip = $counts['shipped_count'];
	$countDile = $counts['delivered_count'];
	$countCanl = $counts['canceled_count'];
} catch (PDOException $err) {
	echo 'Error: ' . $err->getMessage();
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

				<!-- *****************The page Content*******************  -->
				<p>Manage > <a style="text-decoration:none;color:#333" href="Products.php">Orders</a></p>
				<h1>Orders</h1>
				<div class="products_stats">
					<a href="Orders.php">All</a>
					<?php foreach ($AllStatus as $status) { ?>
						<?php
						// Get the count based on status ID
						$count = 0;
						switch ($status['id']) {
							case 1:
								$count = $countPend; // Use the count you retrieved earlier for pending
								break;
							case 2:
								$count = $countProc; // Use the count you retrieved earlier for processing
								break;
							case 3:
								$count = $countShip; // Use the count you retrieved earlier for shipped
								break;
							case 4:
								$count = $countDile; // Use the count you retrieved earlier for delivered
								break;
							case 5:
								$count = $countCanl;
								break;
						}
						?>
						<a href="Orders.php?status=<?= $status['id'] ?>">
							<?= $status['status'] ?> (
							<?= $count ?> )
						</a>
					<?php } ?>
				</div>


				<div class="Filter">
					<input type="text" name="searchBar" id="searchBar" placeholder='Search Products'>
				</div>

				<div class="btn_add-products">
					<a href="addOrder.php" class="btn_add-products">
						<button style="background-color:rgba(0,0,0,0.3);cursor:not-allowed" disabled>Add New
							Order</button>
					</a>
					<a href="status.php" class="btn_add-products">
						<button style="cursor:pointer"><i class="fa-solid fa-gear"></i>Change Status</button>
					</a>
				</div>

				<h1>You Have
					<?= count($orders) ?> New Order
				</h1>

				<section class="user-list">
					<table>
						<tr>
							<th>
								<input type="checkbox" name="" id="">
							</th>
							<th></th>
							<th>User Name</th>
							<th>Numero</th>
							<th>Order Date</th>
							<th>Total Amount</th>
							<th>City</th>
							<th>Street</th>
							<th>Status_Id</th>
							<th>action</th>
						</tr>
						<?php foreach ($orders as $order) { ?>
							<tr style="background-color:<?php

							if ($order['status_id'] === 5) {
								echo 'rgba(255,0,0,0.2);';
							} elseif ($order['status_id'] === 4) {
								echo 'rgba(0,255,0,0.2);';
							}
							?>">
								<td>
									<input type="checkbox" name="" id="">
								</td>
								<td></td>
								<td>
									<?= $order['user_name'] ?>
								</td>
								<td>
									<?= $order['numero'] ?>
								</td>
								<td>
									<?= $order['order_date'] ?>
								</td>
								<td>
									<?= $order['total_amount'] ?>
								</td>
								<td>
									<?= $order['city'] ?>
								</td>
								<td>
									<?= $order['street'] ?>
								</td>
								<td>
									<?= $order['status'] ?>
								</td>
								<td>
									<a href="DeleteOrder.php?id=<?= $order['id'] ?>"><i style="color :red"
											class="fa-solid fa-trash"></i></a>
									<?php if ($order['status_id'] === 1) { ?>
										<a href="viewOrder.php?id=<?= $order['id'] ?>&user=<?= $order['user_name'] ?>"><i
												class="fa-solid fa-question"></i></a>
									<?php } else { ?>
										<a href="order_schedule.php?id=<?= $order['id'] ?>&user_id=<?= $order['user_id'] ?>"><i
												class="fa-solid fa-business-time"></i></a>
									<?php } ?>
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