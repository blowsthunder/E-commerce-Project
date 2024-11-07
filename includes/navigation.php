<?php
include_once('includes/set_session_id.php');

?>



<nav>
    <div class="navbar" id="nav_bar">
        <div class="navbar_logo">
            <a href="index.php"><img src="assets/images/logo.png"></a>
        </div>
        <ul id="menu" class="hidden">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="produit.php">Nos Produit</a></li>
            <li><a href="aboutUs.php">A Propos</a></li>
        </ul>
        <div class="navbar_icons">
            <label for="check"><i class="fa-solid fa-bars"></i></label>
            <a href="#"><i id="pannel" class="fa-solid fa-basket-shopping"></i></a>

            <?php if (isset($_SESSION["Connected"]) && $_SESSION["Connected"] === 1) { ?>
                <i id="dropdown-icon" class="fa-solid fa-caret-down"></i>

                <div id="dropdown-menu" class="hidden">
                    <a href="auth/routeHandler.php"><i class="fa-solid fa-user"></i>
                        <p>Profile</p>
                    </a>
                    <div class="line"></div>
                    <a href="auth/logout.php"><i class="fa-solid fa-arrow-right-from-bracket" style="color:red"></i>
                        <p style="color:red;">Logout</p>
                    </a>
                </div>
            <?php } else { ?>
                <a href="auth/login.php"><i class="fa-regular fa-user"></i></a>
            <?php } ?>
        </div>
        <?php include_once('./Components/pannier.php') ?>
    
    </div>
</nav>
<input type="checkbox" id="check">
<div class="leftPopUp">
    <ul id="menu_phone" class="hidden">
        <li><a href="index.php">Accueil</a></li>
        <li><a href="produit.php">Nos Produit</a></li>
        <li><a href="aboutUs.php">A Propos</a></li>
    </ul>
</div>
<div id="blur-overlay"></div>
<script>
    // Add an event listener to close the menu and hide the blurred overlay
    document.getElementById("blur-overlay").addEventListener("click", function () {
        // Check if the menu is open (the checkbox is checked)
        if (document.getElementById("check").checked) {
            // Uncheck the checkbox to close the menu
            document.getElementById("check").checked = false;
        }
    });

</script>