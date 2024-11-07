 <?php
include_once('../includes/sessionCheck.php');
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

	<!-- The page Content  -->
		<main class="main-boxes">
			<section class="box">
				<h2>Total Sales</h2>
				<p>$5000</p>
			</section>

			<section class="box">
				<h2>New Orders</h2>
				<p>50</p>
			</section>

			<section class="box">
				<h2>Expenses</h2>
				<p>$1500</p>
			</section>

			<section class="box">
				<h2>New Users</h2>
				<p>20</p>
			</section>
    	</main>

    <section class="user-list">
        <h2>Most Interactive Users</h2>
		<table>
  			<tr>
    			<th>User</th>
				<th>Email</th>
				<th>Number</th>
				<th>Action</th>
			</tr>
			<?php for($i=1;$i<=10;$i++){?>
			<tr>
				<td>User <?=$i?></td>
				<td>email<?=$i?>@gmail.com </td>
				<td>066666666</td>
				<td>
					<i class="fa-regular fa-message"></i>
					<i class="fa-solid fa-hand"></i>
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