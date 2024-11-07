<?php
session_start();


// Include database connection
include_once('includes/db_connection.php');

// Get category and filter values from query parameters
$categoryID = isset($_GET['id']) ? $_GET['id'] : '';

// Check if the categoryID is not an integer or is empty
if (!is_numeric($categoryID)) {
    $categoryID = ''; // Set it to an empty string
} else {
    // Convert the categoryID to an integer to ensure it's a valid ID
    $categoryID = (int) $categoryID;
}

// Check if the filter value is valid
$validFilters = ['ASC', 'DESC']; // Define valid filter values
$filter = isset($_GET['filter']) ? strtoupper($_GET['filter']) : 'ASC';

// Validate the filter value
if (!in_array($filter, $validFilters)) {
    $filter = 'ASC'; // Set a default value if the provided value is not valid
}





try {

    // Fetch products based on category and filter
    $productSql = "SELECT p.id, p.name, p.sale_Price, p.image_principale, p.image_hover,
                   p.category_id
                   FROM products p
                   WHERE p.display = 1";

    $productSqlCount = "SELECT count(p.id) as total 
                FROM products p
                WHERE p.display = 1";

    // Fetch available categories
    $categorySql = "SELECT COUNT(*) as count FROM category WHERE id = :category";
    $categoryStmt = $db->prepare($categorySql);
    $categoryStmt->bindValue(':category', $categoryID);
    $categoryStmt->execute();
    $categoryExist = $categoryStmt->fetch(PDO::FETCH_ASSOC);

    // Check if ID exists in category
    if ($categoryExist['count'] > 0) {
        $productSql .= ' AND p.category_id = :category';
        $productSqlCount .= ' AND p.category_id = :category';
        $productStmt = $db->prepare($productSql);
        $statementCount = $db->prepare($productSqlCount);
        $productStmt->bindValue(':category', $categoryID, PDO::PARAM_INT);
        $statementCount->bindValue(':category', $categoryID, PDO::PARAM_INT);
    }

    if (!isset($statementCount)) {
        // If category doesn't exist, query for all products
        $statementCount = $db->prepare($productSqlCount);
    }
    $statementCount->execute();
    $countProduct = $statementCount->fetch(PDO::FETCH_ASSOC);
    //Pagination
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
    $Item_number = 15;
    $Page_number = ceil($countProduct['total'] / $Item_number);
    $start = ($page - 1) * $Item_number;

    $productSql .= " ORDER BY p.sale_price $filter limit $start,$Item_number"; // Double quotes here

    if (!isset($productStmt)) {
        // If category doesn't exist, query for all products
        $productStmt = $db->prepare($productSql);
    }


    $productStmt->execute();
    $products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch category information
    try {
        $sql = 'SELECT * FROM category WHERE id = :category';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':category', $categoryID);
        $stmt->execute();
        $categoryInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        echo 'ERROR: ' . $ex->getMessage();
    }

} catch (PDOException $ex) {
    // Log or handle the exception appropriately
    echo 'Error: ' . $ex->getMessage();
}

// Now, you can use the $products array which contains both product and category information
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/font.css?t=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/footer.css?t=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/navigation.css?t=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/style_all_produit.css?t=<?= time() ?>">
    <title>Document</title>
</head>

<body>
    <!--Navigation Section-->
    <?php include_once('includes/navigation.php') ?>
    <style>
        .navbar {
            position: fixed;
            background-color: white
        }

        .navbar .navbar_logo img {
            filter: none;
        }

        .navbar ul li a {
            color: black;
        }

        .navbar .navbar_icons i {
            color: black;
        }
    </style>

    <section>
        <div class="main_produit">
            <div class="main_image">
                <img
                    src="<?= isset($categoryInfo['image_display']) ? $categoryInfo['image_display'] : 'https://benyahyastore.ma/wp-content/uploads/2020/12/1-78.jpg' ?>">
            </div>
            <div class="main_parag">
                <h1>
                    <?= isset($categoryInfo['name']) ? $categoryInfo['name'] : 'ALL' ?> Clothes
                </h1>
                <p>
                    <?= isset($categoryInfo['description']) ? $categoryInfo['description'] : 'Dive into the world of VukWear Anime Collection, where fashion meets fandom. Discover a range of clothing that pays homage to your favorite anime characters and series. Our carefully curated collection combines high-quality materials with vibrant designs,' ?>
                </p>
            </div>
        </div>
    </section>

    <div class="produit">
        <!-- right_pannel -->
        <div class="right_pannel">
            <div class="dispo">
                <div class="title">
                    <h3>Filter</h3>
                    <i class="fa-solid fa-arrow-up"></i>
                </div>
                <div class="filters">
                    <a href="produit.php?id=<?= isset($_GET['id']) ? $_GET['id'] : '' ?>&filter=ASC"><input
                            type="button" value="Prix Croissant"></a>
                    <a href="produit.php?id=<?= isset($_GET['id']) ? $_GET['id'] : '' ?>&filter=DESC"><input
                            type="button" value="Prix decroissant"></a>
                </div>
            </div>
        </div>


        <!-- left_pannel -->
        <div class="left_pannel">
            <div class="top_vente_header">
                <h1>Top Product</h1>
            </div>
            <div id="pagination">
                <?php for ($i = 1; $i <= $Page_number; $i++) {
                    if ($i != $page) {
                        echo "<a href='?page=$i'> $i </a>";
                    } else {
                        echo "<a href='?page=$i' class='active'> $i </a>";
                    }

                }
                ?>
            </div>
            <div class="top_vente">
                <?php foreach ($products as $product) { ?>
                    <div class="top_vente_item">
                        <div class="image_border">
                            <a href="fiche_produit.php?id=<?= $product['id'] ?>">
                                <img src="<?= $product['image_principale'] ?>">
                                <img class="hover-image" src="<?= $product['image_hover'] ?>" alt="Hover Image">
                            </a>
                            <div class="addquick">
                                <a href="#ficheproduit.php?id=<?= $product["id"] ?>">
                                    <h4>Quick add to Cart</h4>
                                </a>
                            </div>
                        </div>

                        <div class="top_vente_info">
                            <h4>
                                <?= $product['name'] ?>
                            </h4>
                            <h6>
                                <?= $product['sale_Price'] ?> Dhs
                            </h6>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div id="pagination">
                <?php for ($i = 1; $i <= $Page_number; $i++) {
                    if ($i != $page) {
                        echo "<a href='?page=$i'> $i </a>";
                    } else {
                        echo "<a href='?page=$i' class='active'> $i </a>";
                    }

                }
                ?>

            </div>




            <script>
                // Get all the title elements
                const titleElements = document.querySelectorAll('.right_pannel .title');

                // Loop through each title element and add a click event listener
                titleElements.forEach(titleElement => {
                    titleElement.addEventListener('click', () => {
                        // Toggle the 'show' class on the parent 'dispo' element
                        const dispoElement = titleElement.parentNode;
                        dispoElement.classList.toggle('show');
                    });
                });

            </script>

            <script src="app.js?t=<?= time() ?>"></script>
</body>

</html>