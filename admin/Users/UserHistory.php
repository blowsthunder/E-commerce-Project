<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');

$user_id=$_GET['id'];

try {
    $sqlUser = "SELECT nom,numero from users WHERE id = :user";
    $statetment = $db->prepare($sqlUser);
    $statetment ->bindParam(':user',$user_id);
    $statetment ->execute();
    $user = $statetment ->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo 'Error' . $ex;
}

try {
    $sql = "SELECT * from orders WHERE user_id = :user";
    $stat = $db->prepare($sql);
    $stat->bindParam(':user',$user_id);
    $stat->execute();
    $orders = $stat->fetchAll(PDO::FETCH_ASSOC);
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
                <div class="information">
                    <h1>User Information</h1>
                    <h4>UserName : <?= $user['nom']?></h4>
                    <h4>number : <?= $user['numero']?></h4>
                </div>

                <section class="user-list">
                    <h2>User History</h2>
                    <?php
                        if(count($orders)===0){
                            echo "<p style='color:red;'>This User never made an order !</p>";
                        }else{
                    ?>
                    <div class="Filter">
                        <input type="text" name="searchBar" id="searchBar" placeholder='Search Products'>
                    </div>
                    <table>
                        <tr>
                            <th>Id</th>
                            <th>Order Date</th>
                            <th>Total Amount</th>
                            <th>City</th>
                            <th>street</th>
                            <th>status</th>
                            <th>Action</th>
                        </tr>
                        <?php foreach ($orders as $order) { ?>
                            <tr>
                                <td>
                                    <?= $order['id'] ?>
                                </td>
                                <td>
                                    <?= $order['order_date'] ?>
                                </td>
                                <td>
                                    <?= $order['total_amount']?>
                                </td>
                                <td>
                                    <?= $order['city'] ?>
                                </td>
                                <td>
                                    <?= $order['street'] ?>
                                </td>
                                <td>
                                    <?= $order['status_id'] ?>
                                </td>
                                <td>
                                    <a href="viewHistory.php?id=<?= $order['id']?>">View Items</a>
                                </td>
                            </tr>
                        <?php } ?>

                    </table>
                    <?php }?>
                </section>


            </div>
        </div> <!-- main-content -->
    </section> <!-- main -->

    <script src="app.js"></script>
</body>

</html>