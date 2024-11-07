<?php
session_start();
//includes
include_once('includes/db_connection.php');
// Displaying The product 
if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    try {
        $sql = "SELECT * FROM products Where display = 1 and id = $productId";
        $smt = $db->prepare($sql);
        $smt->execute();
        $product = $smt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        echo $ex;
    }

    try {
        $sqlAdd = "SELECT id,photo_url FROM product_photos Where product_id = :product_id";
        $smtAdd = $db->prepare($sqlAdd);
        $smtAdd->bindParam(':product_id', $productId);
        $smtAdd->execute();
        $additional_images = $smtAdd->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        echo $ex;
    }

}

if (isset($_POST['add_to_card'])) {
    if (!isset($_POST['size'])) {
        $message = "Please Select a size";
    } elseif (!isset($_POST['style'])) {
        $message = "Please Select a style";
    } elseif (!isset($_POST['color'])) {
        $message = "Please Select a color";
    } else {
        if ($_SESSION['id'] === session_id()) {
            $cart = 'guest_cart';
            $user_id_column = 'session_id';
        } else {
            $cart = 'cart';
            $user_id_column = 'user_id';
        }

        $selectedSize = $_POST['size'];
        $selectedStyle = $_POST['style'];
        $selectedColor = $_POST['color'];

        try {
            $sql = "SELECT id, quantity FROM $cart WHERE $user_id_column = :user_id AND product_id = :product_id AND size = :size AND style = :style AND color = :color";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $_SESSION['id']);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':size', $selectedSize);
            $stmt->bindParam(':style', $selectedStyle);
            $stmt->bindParam(':color', $selectedColor);
            $stmt->execute();

            $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingProduct) {
                // Product with the same size, style, and color already exists in cart
                $newQuantity = $existingProduct['quantity'] + 1;

                // Update the quantity of the existing product in the cart
                $updateSql = "UPDATE cart SET quantity = :newQuantity WHERE id = :existingId";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->bindParam(':newQuantity', $newQuantity);
                $updateStmt->bindParam(':existingId', $existingProduct['id']);
                $updateStmt->execute();

                $style = "style='padding:20px; background-color: #27AE60; color:white'";
                $message = "<p $style>Product quantity updated in cart</p>";
            } else {
                $sql = "INSERT INTO $cart ($user_id_column, product_id, quantity, size, style, color, created_at) VALUES (:user_id, :product_id, :quantity, :size, :style, :color, NOW())";
                $statement = $db->prepare($sql);

                // Bind the values to placeholders
                $statement->bindParam(':user_id', $_SESSION['id']);
                $statement->bindParam(':product_id', $productId);
                $statement->bindValue(':quantity', 1);
                $statement->bindParam(':size', $selectedSize);
                $statement->bindParam(':style', $selectedStyle);
                $statement->bindParam(':color', $selectedColor);

                // Execute the statement
                $statement->execute();

                if ($statement->rowCount() == 1) {
                    $style = "style='padding:20px; background-color: #27AE60; color:white'";
                    $message = "<p $style>Product added to cart successfully</p>";
                    unset($_POST['size']);
                    unset($_POST['style']);
                    unset($_POST['color']);

                    $active = true;
                }
            }
        } catch (PDOException $ex) {
            $message = $ex->getMessage();
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/navigation.css?t=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/style_produit.css?t=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/font.css?t=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/footer.css?t=<?= time() ?>">

    <title>Document</title>
</head>

<body>
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
    <div class="product-details">
        <div class="product-photos">
            <div class="small-photos">
                <img class="small-photo"
                    src="getImageAPI.php?id=<?= $product['id'] ?>&image_type=image_principale&quality=100"
                    alt="Small Photo 1">
                <img class="small-photo"
                    src="getImageAPI.php?id=<?= $product['id'] ?>&image_type=image_hover&quality=100"
                    alt="Small Photo 2">
                <img class="small-photo"
                    src="getImageAPI.php?id=<?= $product['id'] ?>&image_type=small_image&quality=100"
                    alt="Small Photo 3">
                <?php
                foreach ($additional_images as $image) { ?>
                    <img class="small-photo"
                        src="getImageAPI.php?id=<?= $image['id'] ?>&image_type=additional_image&quality=100"
                        alt="Small Photo 3">
                    <?php
                } ?>
            </div>

            <div class="big-photo">
                <img id="bigPhoto" src="getImageAPI.php?id=<?= $product['id'] ?>&image_type=image_principale&quality=100"
                    alt="Big Photo">
            </div>
        </div>

        <div class="product-info">
            <h1>
                <?= $product["name"] ?>
            </h1>
            <div class="price">
                <p>Price : <span>
                        <?= $product['regular_Price'] ?> Dhs
                    </span> only <span id="span2">
                        <?= $product['sale_Price'] ?> Dhs
                    </span> </p>
            </div>
            <form method="post">
                <!-- erreur_display -->
                <?php if (!empty($message)) { ?>
                    <div id="erreur">
                        <?= $message ?>
                    </div>
                <?php } ?>



                <div class="priceAndstyle">
                    <h3>Size :</h3>
                    <div class="choice">
                        <input type="radio" id="sizeS" name="size" value="S" class="hidden-radio ">
                        <label for="sizeS">S</label><br>

                        <input type="radio" id="sizeM" name="size" value="M" class="hidden-radio ">
                        <label for="sizeM">M</label><br>

                        <input type="radio" id="sizeL" name="size" value="L" class="hidden-radio ">
                        <label for="sizeL">L</label><br>

                        <input type="radio" id="sizeXL" name="size" value="XL" class="hidden-radio ">
                        <label for="sizeXL">XL</label><br>

                        <input type="radio" id="sizeXXL" name="size" value="XXL" class="hidden-radio ">
                        <label for="sizeXXL">XXL</label><br>
                    </div>
                </div>



                <div class="priceAndstyle">
                    <h3>Style :</h3>
                    <div class="choice">
                        <input type="radio" id="styleSimpleFit" name="style" value="Simple Fit" class="hidden-radio ">
                        <label for="styleSimpleFit">Simple Fit</label><br>

                        <input type="radio" id="styleOverSize" name="style" value="Over Size" class="hidden-radio ">
                        <label for="styleOverSize">Over Size</label><br>
                    </div>
                </div>


                <div class="priceAndstyle">
                    <h3>Color :</h3>
                    <div class="choice">
                        <input type="radio" id="colorBlack" name="color" value="Black" <?= ($_SESSION['selected_color'] ?? '') === 'Black' ? 'checked' : '' ?> class="hidden-radio ">
                        <label for="colorBlack">Black</label><br>

                        <input type="radio" id="colorWhite" name="color" value="White" <?= ($_SESSION['selected_color'] ?? '') === 'White' ? 'checked' : '' ?> class="hidden-radio ">
                        <label for="colorWhite">White</label><br>
                    </div>
                </div>




                <input id="button" type="submit" name="add_to_card" value="Add to card" />
                <div class="catchers">
                    <div class="catcher">
                        <i style="color:green" class="fa-solid fa-circle"></i>
                        <p>En stock</p>
                    </div>
                    <div class="catcher">
                        <i class="fa-solid fa-check-double"></i>
                        <p>7 jours de garantie : Satisfait ou remboursé</p>
                    </div>
                    <div class="catcher">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <p>Paiment a livraison </p>
                    </div>
                </div>

                <div class="Dropdown">
                    <div class="title">
                        <h3>Description:</h3>
                        <i id="description-icon" class="fa-solid fa-caret-down"></i>
                    </div>
                    <div class="information_desc">
                        <div class="line"></div>
                        <p>
                            <?= $product['description'] ?>
                        </p>
                    </div>
                </div>

                <div class="Dropdown">
                    <div class="title">
                        <h3>Quality:</h3>
                        <i id="quality-icon" class="fa-solid fa-caret-down"></i>
                    </div>
                    <div class="information_qual">
                        <div class="line"></div>
                        <ul>
                            <li>Reason 1</li>
                            <li>Reason 2</li>
                            <li>Reason 3</li>
                        </ul>
                    </div>
                </div>

                <div class="Dropdown">
                    <div class="title">
                        <h3>Remboursé:</h3>
                        <i id="remboursement-icon" class="fa-solid fa-caret-down"></i>
                    </div>
                    <div class="information">
                        <div class="line"></div>
                        <ul>
                            <li>Reason for ex</li>
                        </ul>
                    </div>
                </div>

            </form>
        </div>

    </div>



    <?php
    $_GET['category_id'] = $product['category_id'];
    include('Components/ProduitSimilaire.php'); ?>


    <?php include('includes/footer.php') ?>
             

    
    <script>
        const bigPhoto = document.getElementById('bigPhoto');
        const smallPhotos = document.querySelectorAll('.small-photo');

        smallPhotos.forEach(photo => {
            photo.addEventListener('click', () => {
                bigPhoto.src = photo.src;
            });
        });

        // Get all the title elements and icon elements
        document.addEventListener("DOMContentLoaded", function () {
            const sizeOverlays = document.querySelectorAll("#sizeChoice .choice-overlay");
            const styleOverlays = document.querySelectorAll("#styleChoice .choice-overlay");
            const colorOverlays = document.querySelectorAll("#colorChoice .choice-overlay");

            sizeOverlays.forEach(overlay => {
                overlay.addEventListener("click", function () {
                    const value = this.getAttribute("data-value");
                    const input = this.nextElementSibling;

                    sizeOverlays.forEach(overlay => overlay.classList.remove("selected"));
                    this.classList.add("selected");

                    input.value = value;
                    input.click();
                });
            });

            styleOverlays.forEach(overlay => {
                overlay.addEventListener("click", function () {
                    const value = this.getAttribute("data-value");
                    const input = this.nextElementSibling;

                    styleOverlays.forEach(overlay => overlay.classList.remove("selected"));
                    this.classList.add("selected");

                    input.value = value;
                    input.click();
                });
            });

            colorOverlays.forEach(overlay => {
                overlay.addEventListener("click", function () {
                    const value = this.getAttribute("data-value");
                    const input = this.nextElementSibling;

                    colorOverlays.forEach(overlay => overlay.classList.remove("selected"));
                    this.classList.add("selected");

                    input.value = value;
                    input.click();
                });
            });

            // Get all the title elements and icon elements
            const titles = document.querySelectorAll('.title');
            const icons = document.querySelectorAll('.fa-caret-down');

            // Attach click event listeners to each title
            titles.forEach((title, index) => {
                title.addEventListener('click', function () {
                    // Toggle the active class to rotate the icon
                    this.classList.toggle('actives');

                    // Toggle the max-height property to reveal/hide the related information section
                    const information = this.nextElementSibling;
                    information.style.maxHeight = information.style.maxHeight ? null : information.scrollHeight + 'px';
                });
            });
        });




    </script>

    <script src="app.js?t=<?= time() ?>"></script>

    <?php if(isset($active)){
        echo '<script> 
        const blur = document.querySelector(".card_pannel_screen_blur");
        blur.classList.add("active");</script>';
    }?>   
</body>

</html>