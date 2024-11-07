<?php
try {
    if (isset($_GET['category_id'])) {
        $idCategorie = $_GET['category_id'];

        // You can add your SQL query here that uses $idCategorie
        $sql = "SELECT id, name, sale_Price, image_principale, image_hover FROM products WHERE category_id = :idCategorie AND display = 1";
        $smt = $db->prepare($sql);
        $smt->bindParam(':idCategorie', $idCategorie, PDO::PARAM_INT);
    } else {
        // You can add your default SQL query here
        $sql = "SELECT id, name, sale_Price, image_principale, image_hover FROM products WHERE display = 1";
        $smt = $db->prepare($sql);
    }

    $smt->execute();
    $products = $smt->fetchAll(PDO::FETCH_ASSOC);
    // You can process and return $products as needed
} catch (PDOException $ex) {
    // Log the error or return a user-friendly error message
    echo "Error: " . $ex->getMessage();
}
?>
<!--Top Vente Section-->
<div class="top_vente_section">
    <div class="top_vente_header">
        <h1>Produit Similaire</h1>
    </div>
    <div class="top_vente">
        <?php foreach ($products as $product) { ?>
            <div class="top_vente_item">
                <div class="image_border">
                    <a href="fiche_produit.php?id=<?= $product['id'] ?>">
                        <img src="getImageAPI.php?id=<?= $product['id']?>&image_type=image_principale&quality=100">
                        <img class="hover-image" src="getImageAPI.php?id=<?= $product['id']?>&image_type=image_hover&quality=100" alt="Hover Image">
                    </a>
                    <div class="addquick">
                        <a href="#ficheproduit.php?id=<?= $product["id"] ?>">
                            <h4>View Product</h4>
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
</div>