<?php
session_start();

//includes
include_once('includes/db_connection.php');


try {
    $sql = "SELECT id,image,name FROM category";
    $smt = $db->prepare($sql);
    $smt->execute();
    $categorys = $smt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo $ex;
}


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
    <link rel="stylesheet" href="assets/css/navigation.css?t=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/styles.css?t=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/footer.css?t=<?= time() ?>">
    <title>Document</title>
</head>

<body>
    <?php include_once('includes/navigation.php') ?>
    <?php include_once('includes/deleteItems.php') ?>


    <!--Main Section-->
    <div class="main">
        <img src="assets/images/bg1.jpg" alt="bg1" id="background_image">
        <div class="catcher">
            <h4 id="catcher-h4">Find your perfect</h4>
            <h1 id="catcher-h1">T-shirt</h1>
        </div>
    </div>

    <!--Why us Section ?-->
    <section>
        <div class="rapper">
            <h1>Why us ?</h1>
            <div class="reasons">
                <!-- Reason1 -->
                <div class="reason_item">
                    <img src="assets/images/quality.png" alt="quality">
                    <div class="the_reason">
                        <h4>Qualité</h4>
                        <p>Chez VukWear, la qualité n'est pas seulement un trait - <span style="font-weight:bold"> c'est
                                un mode de vie.</span></p>
                    </div>
                </div>
                <!-- Reason2 -->
                <div class="reason_item">
                    <img src="assets/images/shipped.png" alt="quality">
                    <div class="the_reason">
                        <h4>Livraison Rapide</h4>
                        <p>Élevez votre confiance avec VukWear : Des Vêtements de Qualité, un Style Inégalé, et une
                            <span style="font-weight:bold">Livraison Rapide</span> !
                        </p>
                    </div>
                </div>
                <!-- Reason3 -->
                <div class="reason_item">
                    <img src="assets/images/paiement-a-la-livraison.png" alt="quality">
                    <div class="the_reason">
                        <h4>Paiement à la Livraison</h4>
                        <p>Alignez vos Valeurs avec VukWear : Qualité Exceptionnelle, Style Incomparable et <span
                                style="font-weight:bold">Paiement avec Livraison.</span></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Categories-->
    <section>
        <div class="top_vente_header">
            <h1>Notre Collection</h1>
        </div>
        <div class="categorie_section">
            <div class="all_categories">
                <?php foreach ($categorys as $category) { ?>
                    <div class="categorie_item">
                        <a href="produit.php?id=<?= $category['id'] ?>">
                            <img src="<?= $category['image'] ?>">
                            <h1>
                                <?= $category['name'] ?>
                            </h1>
                            <div class="categories_view">
                                <h4>View Categorie</h4>
                            </div>
                            <a href="produit.php">
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!--Top Vente Section-->
    <?php
    foreach ($categorys as $category) {
        $id = $category['id'];
        // Set the $id as a variable before including DisplayProduct.php
        include('Components/DisplayProduct.php');
    }
    ?>



    <!--About Us Section-->
    <div class="question">
        <div class="left_question">
            <h3>Questions fréquentes</h3>
            <h1>FAQ</h1>
            <h6>Notre service client est disponible tout les jours : de 8h00 à 20h30</h6>
        </div>

        <div class="right_question">
            <div class="Question">
                <h4>Quel est le delai moyen de Livraison ?</h4>
                <i class="fa-solid fa-angle-up"></i>
            </div>
            <div class="reponse">
                <p> Le délai moyen de livraison de nos produits est généralement de de 2 à 5 jours</p>
            </div>
            <div id="lineQuestio"></div>

            <div class="Question">
                <h4>Offront vous des livraison gratuite ?</h4>
                <i class="fa-solid fa-angle-up"></i>
            </div>
            <div class="reponse">
                <p> Nous offrons la livraison gratuite à partir d'un panier d'achat de 3 pieces</p>
            </div>
            <div id="lineQuestio"></div>

            <div class="Question">
                <h4>Ma commande est endomagée que dois je faire ?</h4>
                <i class="fa-solid fa-angle-up"></i>
            </div>
            <div class="reponse">
                <p>Dans les rares cas où votre commande est livrée endommagée ou si vous remarquez un défaut, faites-le
                    nous savoir dès que vous recevez votre commande et nous la remplacerons immédiatement pour vous !
                    Pour commencer: Prenez quelques photos montrant clairement le problème avec la toile, ainsi que des
                    photos de l'avant et de l'arrière de la toile, et de la boîte dans laquelle elle a été expédiée (si
                    la boîte était également endommagée). Contactez le contact@kolors.ma et remplissez le formulaire en
                    bas de la page. Veuillez inclure votre nom complet et votre numéro de commande tels qu'ils
                    apparaissent sur votre e-mail de confirmation de commande. Téléchargez les photos et fournissez un
                    bref résumé du problème. Soumettez le formulaire et notre équipe d'assistance vous répondra dans les
                    24 heures</p>
            </div>

        </div>
    </div>

    <?php include_once('includes/footer.php') ?>
    <?php include_once('Whatapp.php') ?>
    <script>

        // Image Changing and catcher changing
        const images = ["bg1.jpg", "bg2.jpg"]; // List of image filenames
        const imgElement = document.querySelector('.main img');
        const h1Element = document.getElementById('catcher-h1');
        const h4Element = document.getElementById('catcher-h4');
        const bgImage = document.getElementById('background_image');
        let currentImageIndex = 0;
        let shouldRestartInterval = true; // Flag to control interval restart

        const contentData = [
            { h1: "Unleash the Demon Within", h4: "Forged in the Gym, Worn with Stoic Pride" },
            { h1: "the Right Clothes", h4: "Begin Your Journey to Success with" },
            // Add more data as needed
        ];

        let isAnimating = false; // Flag to prevent concurrent animations

        function rotateContent() {
            if (isAnimating) {
                return; // Don't trigger animation if it's already running
            }

            isAnimating = true; // Set flag to indicate animation is running
            const currentData = contentData[currentImageIndex % contentData.length];
            h1Element.style.opacity = 0;
            h4Element.style.opacity = 0;
            imgElement.style.opacity = 0;
            imgElement.style.transform = 'translateY(-300px)';


            setTimeout(() => {
                imgElement.src = "assets/images/" + images[currentImageIndex % images.length];
                imgElement.style.opacity = 1;
                imgElement.style.transform = "translateY(0px)";

                h1Element.textContent = currentData.h1;
                h4Element.textContent = currentData.h4;
                h1Element.style.transform = "translateX(-200px)";
                h4Element.style.transform = "translateX(-200px)";

                setTimeout(() => {
                    h1Element.style.opacity = 1;
                    h1Element.style.transform = "translateX(0px)";
                    h4Element.style.opacity = 1;
                    h4Element.style.transform = "translateX(0px)";

                    isAnimating = false; // Reset flag after animation completes
                }, 600);

            }, 200);

            currentImageIndex++;

            if (shouldRestartInterval) {
                clearInterval(rotationInterval);
                rotationInterval = setInterval(rotateContent, 5000);
            }
        }

        // Call the rotateContent function every 5 seconds
        let rotationInterval = setInterval(rotateContent, 5000); // 5000 milliseconds = 5 seconds

        bgImage.addEventListener('click', () => {
            shouldRestartInterval = false; // Don't restart interval immediately
            rotateContent();
            shouldRestartInterval = true; // Allow interval restart after this click
        });



        //Question et reponse 
        const questions = document.querySelectorAll('.Question');

        questions.forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const arrow = question.querySelector('.fa-angle-up');

                answer.classList.toggle('show');
                arrow.style.transform = answer.classList.contains('show') ? 'rotate(-180deg)' : 'rotate(0deg)';
            });
        });
    </script>
    <script src="app.js?t=<?= time() ?>"></script>

</body>

</html>