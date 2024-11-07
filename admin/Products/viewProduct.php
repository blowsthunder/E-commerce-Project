<?php
// Includes
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');
include_once('../includes/form.php');
include_once('../includes/sanitize_input.php');

// Check if product ID is provided in the URL
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    try {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        var_dump($product);
    } catch (PDOException $ex) {
        $message = 'Error adding product: ' . $ex->getMessage();
    }


}

//Retrieve number of Category and it ID
try {
    $sql = "SELECT id,name from category";
    $statement = $db->prepare($sql);
    $statement->execute();
    $categorys = $statement->fetchAll(PDO::FETCH_ASSOC);
    if (count($categorys) === 0) {
        $message = "Un error est produit lors de la connection avec Database";
    }
} catch (PDOException $ex) {
    $message = "Error : " . $ex;
}

try {
    $sqladd = "SELECT id,product_id,photo_url from product_photos";
    $statementadd = $db->prepare($sqladd);
    $statementadd->execute();
    $additional_photos = $statementadd->fetchAll(PDO::FETCH_ASSOC);
    if (count($additional_photos) === 0) {
        $message = "Un error est produit lors de la connection avec Database";
    }
} catch (PDOException $ex) {
    $message = "Error : " . $ex;
}



// Handle form submission (when "Save" button is clicked)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['discard'])) {
        header("Location: Products.php"); // Redirect to the same page
        exit();
    } elseif (isset($_POST['publish'])) {
        $display = 1; // Set display to published mode
    }

    if ($_POST['category'] === 'Category') {
        $message = 'Select a category';
    } else {
        foreach ($_POST as $key => $value) {
            if ($key !== 'save_draft' && $key !== 'publish' && $key !== 'stock') {
                if (empty($value)) {
                    $field = str_replace("_", " ", $key); // Convert "key_name" to "Key Name"
                    $field = ucfirst($field); // Capitalize the first letter of each word
                    $message .= "The $field is empty </br>";
                    $checkfield = false;
                } else {
                    $checkfield = true;
                }
            }
        }
    }

    if (empty($message) && $checkfield === true) {
        // Sanitize and validate other inputs
        $name = sanitize_input($_POST['name']);
        $regular_Price = sanitize_input($_POST['regular_Price']);
        $sale_Price = sanitize_input($_POST['sale_price']);
        $description = sanitize_input($_POST['description']);
        $image_principale = sanitize_input($_POST['image_principale']);
        $image_hover = sanitize_input($_POST['image_hover']);
        $small_image = sanitize_input($_POST['small_image']);

        try {
            // Prepare and execute the query
            $updateQuery = "UPDATE products SET 
            name = :name,
            regular_Price = :regularPrice,
            sale_Price = :salePrice,
            description = :description,
            category_id = :category_id,
            image_principale = :imagePrincipale,
            image_hover = :imageHover,
            small_image = :smallImage,
            stock = :stock,
            join_date = NOW(),
            display = :display
            WHERE id = :id";
            $stmt = $db->prepare($updateQuery);

            // Bind the values to placeholders
            $stmt = $db->prepare($updateQuery);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':regularPrice', $regular_Price);
            $stmt->bindParam(':salePrice', $sale_price);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':category_id', $category);
            $stmt->bindParam(':imagePrincipale', $image_principale); // Corrected parameter name
            $stmt->bindParam(':imageHover', $image_hover); // Corrected parameter name
            $stmt->bindParam(':smallImage', $small_image); // Corrected parameter name
            $stmt->bindParam(':stock', $stock);
            $stmt->bindParam(':display', $display);
            $stmt->bindParam(':id', $productId, PDO::PARAM_INT);

            $stmt->execute();

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'additional_image_') === 0) {
                    $photoId = substr($key, strlen('additional_image_'));
                    $photoUrl = sanitize_input($value);
            
                    // Update the additional image in the database using $photoId and $photoUrl
                    $sqlPhoto = "UPDATE product_photos SET photo_url = :photourl WHERE id = :photoid";
                    $statementphoto = $db->prepare($sqlPhoto);
                    $statementphoto->bindParam(':photourl', $photoUrl);
                    $statementphoto->bindParam(':photoid', $photoId, PDO::PARAM_INT);
                    $statementphoto->execute();
                }
            }

            if ($stmt->rowCount() == 1 ) {
                $message = '<span class="success">Product Updated successfully</span>';          

            }
        } catch (PDOException $ex) {
            $message = 'Error adding product: ' . $ex->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<!-- SVG Headers -->
<?php include_once('../includes/Header.php') ?>

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



        <form method="post">
            <div id="main-content">
                <div id="main-content__container">

                    <!-- *****************The page Content*******************  -->
                    <p>Manage > <a style="text-decoration:none;color:#333" href="Products.php">Products</a> > <a
                            style="text-decoration:none;color:#333" href="addProducts.php">Add Products</a></p>
                    <div class="title_and_btn">
                        <div class="Title">
                            <h1>Add a product</h1>
                            <h5>Orders placed across your store</h5>
                        </div>
                        <div class="btn">
                            <button name="discard" type="submit">Return</button>
                            <button name="publish" type="submit">Save</button>
                        </div>
                    </div>

                    <!-- erreur_display -->
                    <?php if (!empty($message)) { ?>
                        <div id="erreur">
                            <?= $message ?>
                        </div>
                    <?php } ?>

                    <div class="product_title">
                        <h3>Product Category</h3>
                        <select id="category" name="category">
                            <option value="Category" <?= (@$category === 'Category') ? ' selected' : '' ?>>Category
                            </option>
                            <?php foreach ($categorys as $categorie) { ?>
                                <option value="<?= $categorie["id"] ?>" <?= (@$category === $categorie['name']) ? ' selected' : '' ?>><?= $categorie['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="product_title">
                        <h3>Product Title</h3>
                        <input type="text" name="name" placeHolder="Write title Here ..."
                            value="<?= $product['name'] ?>">
                    </div>

                    <div class="product_description">
                        <h3>Product Description</h3>
                        <textarea name="description" placeholder="Write description Here ..."
                            rows="4"><?= $product['description'] ?></textarea>
                    </div>

                    <div class="product_images">
                        <h3>Product Images</h3>
                        <div class="viewImage">
                            <img src="<?= $product['image_principale'] ?>">
                            <textarea name="image_principale" placeholder="Write The link for the Principal Image ..."
                                rows="1"><?= $product['image_principale'] ?></textarea>
                        </div>

                        <div class="viewImage">
                            <img src="<?= $product['image_hover'] ?>">
                            <textarea name="image_hover" placeholder="Write The link for the Principal Image ..."
                                rows="1"><?= $product['image_hover'] ?></textarea>
                        </div>

                        <div class="viewImage">
                            <img src="<?= $product['small_image'] ?>">
                            <textarea name="small_image" placeholder="Write The link for the Principal Image ..."
                                rows="1"><?= $product['small_image'] ?></textarea>
                        </div>

                        <?php foreach($additional_photos as $photo){?>
                            <div class="viewImage">
                            <img src="<?= $photo['photo_url'] ?>">
                            <textarea name="additional_image_<?= $photo['id']?>" placeholder="Write The link for the additional Image <?= $photo['id'] ?>..."
                                rows="1"><?= $photo['photo_url'] ?></textarea>
                        </div>   
                            
                        <?php }?>

                    </div>


                    <div class="product_pricing">
                        <h3>Product Pricing</h3>
                        <div class="prices" style="display:flex">
                            <input type="number" name="regular_Price" placeHolder="Regular Price"
                                value="<?= $product['regular_Price'] ?>">
                            <div style="margin:10px;"></div>
                            <input type="number" name="sale_price" id="" placeHolder="Sale Price"
                                value="<?= $product['sale_Price'] ?>">
                        </div>
                    </div>

                    <div class="product_Add-to-Stock">
                        <h3>Add to Stock</h3>
                        <input type="number" name="stock" id="" placeHolder="Add quantity Here ..."
                            value="<?= $product['stock'] ?>">
                    </div>

                </div> <!-- main-content -->
        </form>
    </section> <!-- main -->

    <script src="../app.js"></script>
</body>

</html>