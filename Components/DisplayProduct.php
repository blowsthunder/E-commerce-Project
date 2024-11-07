<?php
if(isset($id)){
    try {
        $sql = "SELECT id,name,sale_Price,image_principale,image_hover FROM products Where display = 1 
        AND category_id= :id LIMIT 5";
        $smt = $db->prepare($sql);
        $smt->bindParam(':id',$id);
        $smt->execute();
        $products = $smt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        echo $ex;
    }
}else{
    echo 'no Id provided';
}

?>


<!--Top Vente Section-->
<div class="top_vente_section">
    <div class="top_vente_header">
        <h1>Top Product</h1>
    </div>
    <div class="top_vente">
        <?php foreach ($products as $product) { ?>
            <div class="top_vente_item">
                <div class="image_border">
                    <a href="fiche_produit.php?id=<?= $product['id'] ?>">
                        <img src="getImageAPI.php?id=<?= $product['id'] ?>&image_type=image_principale">
                        <img class="hover-image" src="getImageAPI.php?id=<?= $product['id'] ?>&image_type=image_hover"
                            alt="Hover Image">
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