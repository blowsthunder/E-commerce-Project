<?php
include_once('../includes/sessionCheck.php');
ini_set('upload_max_filesize', '30M');
error_reporting(E_ALL);
// Variables
$message = "";
$name = $price = $description = $image_principale = $image_hover = $small_image = $category = "";
$regular_Price = $sale_price = $stock = "";
$error = error_get_last();
if ($error !== null) {
    echo $error;
}
//Function save Image
function saveImage($inputName)
{
    if (!empty($_FILES[$inputName]['tmp_name'])) {
        // Define the path where you want to save the image
        $uploadDirectory = '../image/';
        $fileExtension = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
        $uniqueFilename = $inputName . '_' . time() . '.' . $fileExtension; // Generate a unique name

        // Move the uploaded file to the target directory with the unique filename
        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDirectory . $uniqueFilename)) {
            // Return the path to the saved image
            return 'admin/image/' . $uniqueFilename;
        }
    }
    // Return false if the upload fails or no file is selected
    echo 'false';
    return false;



}


// Includes
include_once('../includes/db_connection.php');
include_once('../includes/form.php');
include_once('../includes/sanitize_input.php');

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


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['discard'])) {
        header("Location: addProduct.php"); // Redirect to the same page
        exit();
    } elseif (isset($_POST['save_draft'])) {
        $display = 0; // Set display to draft mode
    } elseif (isset($_POST['publish'])) {
        $display = 1; // Set display to published mode
    }

    //Categories
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
        $sale_Price = sanitize_input($_POST['sale_price']);
        $description = sanitize_input($_POST['description']);
        $additional_images = [];
        
        for ($i = 1; isset($_FILES['additional_image_' . $i]); $i++) {
            $additional_images[$i] = saveImage('additional_image_' . $i);
        }

        

        // Upload and save images first
        $image_principale = saveImage('image_principale');
        $image_hover = saveImage('image_hover');
        $small_image = saveImage('small_image');

        if ($image_principale && $image_hover && $small_image) {
            try {
                // Prepare and execute the query to insert product information
                $query = "INSERT INTO products (name, regular_Price, sale_Price, description, category_id, image_principale, image_hover, small_image, stock, join_date, display)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
                $stmt = $db->prepare($query);

                // Bind the values to placeholders for product information
                $stmt->bindParam(1, $name);
                $stmt->bindParam(2, $regular_Price);
                $stmt->bindParam(3, $sale_Price);
                $stmt->bindParam(4, $description);
                $stmt->bindParam(5, $category);
                $stmt->bindParam(6, $image_principale);
                $stmt->bindParam(7, $image_hover);
                $stmt->bindParam(8, $small_image);
                $stmt->bindParam(9, $stock);
                $stmt->bindParam(10, $display);

                $stmt->execute();

                if ($stmt->rowCount() == 1) {
                    // Get the ID of the newly inserted product
                    $productId = $db->lastInsertId();


                    // Insert additional images into the new table
                    if (!empty($additional_images)) {
                        $additionalImagesQuery = "INSERT INTO product_photos (product_id, photo_url) VALUES (?, ?)";
                        $additionalImagesStmt = $db->prepare($additionalImagesQuery);
                        foreach ($additional_images as $path) {

                        $additionalImagesStmt->execute([$productId, $path]);
                        }
                    }


                    $message = '<span class="success">Product added successfully</span>';
                }
            } catch (PDOException $ex) {
                $message = 'Error adding product: ' . $ex->getMessage();
            }
        }
    }

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



        <form method="post" enctype="multipart/form-data">
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
                            <button name="discard" type="submit">Discard</button>
                            <button name="save_draft" type="submit">Save draft</button>
                            <button name="publish" type="submit">Publish product</button>
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
                                <option value="<?= $categorie["id"] ?>" <?= (@$category === $categorie["name"]) ? ' selected' : '' ?>>
                                    <?= $categorie["name"] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="product_title">
                        <h3>Product Title</h3>
                        <input type="text" name="name" placeHolder="Write title Here ..." value="<?= @$name ?>">
                    </div>

                    <div class="product_description">
                        <h3>Product Description</h3>
                        <textarea name="description" placeholder="Write description Here ..."
                            rows="4"><?= @$description ?></textarea>
                    </div>

                    <div class="product_images">
                        <h3>Product Images</h3>
                        <input type="file" name="image_principale">
                        <input type="file" name="image_hover">
                        <input type="file" name="small_image">
                        <label for="additional_images">Additional Images:</label>
                        <div id="additional-images">
                            <!-- Existing additional image fields, if any, can go here -->
                        </div>
                        <button type="button" id="add-image">Add Additional Image</button>
                    </div>


                    <div class="product_pricing">
                        <h3>Product Pricing</h3>
                        <div class="prices" style="display:flex">
                            <input type="number" name="regular_Price" placeHolder="Regular Price"
                                value="<?= @$regular_Price ?>">
                            <div style="margin:10px;"></div>
                            <input type="number" name="sale_price" id="" placeHolder="Sale Price"
                                value="<?= @$sale_price ?>">
                        </div>
                    </div>

                    <div class="product_Add-to-Stock">
                        <h3>Add to Stock</h3>
                        <input type="number" name="stock" id="" placeHolder="Add quantity Here ..."
                            value="<?= @$stock ?>">
                    </div>

                </div> <!-- main-content -->
        </form>
    </section> <!-- main -->

    <script src="../app.js"></script>
    <script>
        // JavaScript to add a new textarea for additional images
        document.addEventListener("DOMContentLoaded", function () {
            const addImageButton = document.getElementById("add-image");
            const additionalImagesContainer = document.getElementById("additional-images");
            let imageCount = 1;

            addImageButton.addEventListener("click", function () {
                const newImageInput = document.createElement("input");
                newImageInput.type = "file";
                newImageInput.name = "additional_image_" + imageCount; // Use [] for array-like behavior
                additionalImagesContainer.appendChild(newImageInput);
                imageCount++;
            });
        });
    </script>
</body>

</html>