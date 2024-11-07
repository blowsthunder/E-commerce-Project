<?php
//includes
include_once('includes/db_connection.php');
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
    <link rel="stylesheet" href="assets/css/style_about_us.css?t=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/footer.css?t=<?= time()?>">
    <title>Document</title>
</head>

<body>
    <style>
        .navbar {
            position: fixed;
            background-color: white;
            z-index: 0;
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
    <?php include_once('includes/navigation.php') ?>
    <div class="about_us">
        <div class="Header">
            <h1>A propos</h1>
        </div>
        <h2>Who are we ?</h2>
        <p>
            Determination is what drives a man to pursue his own goals! It's the driving force that led
            mankind to achieve the impossibility of something as mankind walked the moon or transformed
            Rome from mere stones into a city of marvels under the leadership of Julius Caesar. It is
            also what sparked Thomas Edison's invention of the light bulb! illuminating our world! from
            as big as these achievements to small individual achievements of our era like Ronnie Coulman
            securing 8 Olympia to David Goggins running 100 miles for 18 hours none stop, proving that
            determination transcends personal limitations
        </p>
        <p>Here are 2 friends who believe and embrace freedom and building confidence, united in
            their pursuit of setting and achieving goals and space, and had an idea of creating
            their own masterpiece ! </p>
        <h2>What is vukWear ?</h2>
        <p>VukWear is more than just a brand – it's a statement of strength, style, and determination.
            We believe in combining top-quality clothing with a spirit of resilience and pride.
            Inspired by the fearless wolf, VukWear represents our commitment to pushing limits,
            achieving greatness, and living with both power and elegance. Every piece of clothing
            we create reflects these values, giving you not only a great look but also the essence
            of your goals. With VukWear, we want to show that you can overcome challenges and
            look amazing while doing it. Because true strength comes both from the inside and outside.</p>
    </div>


    <!-- wHAT DO WE BLIEVE in -->

    <div class="rapper">
        <h1>What Do We Blieve In ?</h1>
        <div class="reasons">
            <!-- Reason1 -->
            <div class="reason_item">
                <img src="assets/images/quality.png" alt="quality">
                <div class="the_reason">
                    <h4>Our Quality</h4>
                    <p>At VukWear, quality isn't just a trait - it's a way of life.</p>
                </div>
            </div>
            <!-- Reason2 -->
            <div class="reason_item">
                <img src="assets/images/value.png" alt="quality">
                <div class="the_reason">
                    <h4>Our Value</h4>
                    <p>At VukWear, our values are stitched into each piece,
                        a testament to our unwavering commitment to quality,
                        strength, and the pursuit of greatness! !
                    </p>
                </div>
            </div>
            <!-- Reason3 -->
            <div class="reason_item">
                <img src="assets/images/motivation.png" alt="quality">
                <div class="the_reason">
                    <h4>Your Motivation</h4>
                    <p>Unlock Your Inner Power:In VukWear Wear What Inspires You and
                        motivate you</p>
                </div>
            </div>
            <!--Reason 4 -->
            <div class="reason_item">
                <img src="assets/images/paiement-a-la-livraison.png" alt="quality">
                <div class="the_reason">
                    <h4>Why Payment on Delivery ?</h4>
                    <p>Experience perfection with VukWear: Pay on delivery,
                        showcasing our trust in quality craftsmanship and your
                        satisfaction.</span></p>
                </div>
            </div>
        </div>
    </div>
    <script src="app.js?t=<?= time() ?>"></script>
</body>