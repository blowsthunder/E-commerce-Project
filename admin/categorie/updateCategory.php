<?php
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');
include_once('../includes/form.php');
include_once('../includes/sanitize_input.php');

$message="";

$category_id = $_GET['id'];
try {
    $sqlItems = "SELECT * FROM category WHERE id= :id";
    $statetment = $db->prepare($sqlItems);
    $statetment->bindParam(':id', $category_id);
    $statetment->execute();
    $category = $statetment->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo 'Error' . $ex;
}

// Handle form submission (when "Save" button is clicked)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['discard'])) {
        header("Location: categorie.php"); // Redirect to the same page
        exit();
    }

        foreach ($_POST as $key => $value) {
            if($key !== 'save_draft' && $key !== 'publish'){
            if (empty($value)) {
                $field = str_replace("_", " ", $key); // Convert "key_name" to "Key Name"
                $field = ucfirst($field); // Capitalize the first letter of each word
                $message .= "The $field is empty </br>";
                $checkfield = false;
            }else{
                $checkfield = true;
            }
        }
    }


    if (empty($message) && $checkfield === true) {
        // Sanitize and validate other inputs
        $name = sanitize_input($_POST['name']);
        $image = sanitize_input($_POST['image']);
        $image_display= sanitize_input($_POST['image_display']);
        $description = sanitize_input($_POST['description']);

        try {
            // Prepare and execute the query
            $updateQuery = "UPDATE category SET 
            name = :name,
            image = :image,
            image_display = :image_display,
            description = :description 
            WHERE id= :id; ";
            $stmt = $db->prepare($updateQuery);
            
            // Bind the values to placeholders
            $stmt = $db->prepare($updateQuery);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':image_display', $image_display);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $category_id,);
        
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $message = '<span class="success">Category Updated successfully</span>';
                header('location:categorie.php');
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

                    <!-- The page Content  -->
                    <div class="title_and_btn">
                        <div class="Title">
                            <h1>Update a category</h1>
                            <h5>Orders placed across your store</h5>
                        </div>
                        <div class="btn">
                            <button name="discard" type="submit">Return</button>
                            <button name="publish" type="submit">Save</button>
                        </div>
                    </div>

                    <!-- //Form -->

                    <!-- erreur_display -->
                    <?php if (!empty($message)) { ?>
                        <div id="erreur">
                            <?= $message ?>
                        </div>
                    <?php } ?>


                    <div class="product_title">
                        <h3>Category Title</h3>
                        <input type="text" name="name" placeHolder="Write title Here ..."
                            value="<?= $category['name'] ?>">
                    </div>

                    <div class="product_description">
                        <h3>Category Description</h3>
                        <textarea name="description" placeholder="Write description Here ..."
                            rows="4"><?= $category['description'] ?></textarea>
                    </div>

                    <div class="product_images">
                        <h3>Category Images</h3>
                        <div class="image">
                            <img src="<?= $category['image'] ?>">
                            <div class="input">
                                <h3>Image</h3>
                                <textarea name="image" placeholder="Write The link for the Principal Image ..."
                                    rows="1"><?= $category['image'] ?></textarea>
                            </div>
                        </div>
                        <div class="image">
                            <img src="<?= $category['image_display'] ?>">
                            <div class="input">
                                <h3>Image Display</h3>
                                <textarea name="image_display" placeholder="Write The link for the Hover Image ..."
                                    rows="1"><?= $category['image_display'] ?></textarea>
                            </div>
                        </div>
                    </div>
                </div> <!-- main-content -->
        </form>
    </section> <!-- main -->


    </div>
    </div> <!-- main-content -->
    </section> <!-- main -->

    <script src="app.js"></script>
</body>

</html>