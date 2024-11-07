<?php
//variables
$nom = "";
$prenom = "";
$email = "";
$numero = "";
$password = "";
$message = "";


//includes
include_once('../includes/db_connection.php');
include_once('../includes/form.php');

//redirection


//Code 

// Handling The register
if (isset($submit)) {
    //Get number and email from Database to checkit
    //numero
    $sel = $db->prepare("select * from users where numero=? limit 1");
    $sel->execute(array(@$_POST["numero"]));
    if ($sel) {
        $tab_num = $sel->fetch(PDO::FETCH_ASSOC);
    }

    //email
    $sel = $db->prepare("select * from users where email=? limit 1");
    $sel->execute(array(@$_POST["email"]));
    if ($sel) {
        $tab_email = $sel->fetch(PDO::FETCH_ASSOC);
    }

    // Check that all fields are filled in
    $style = "style='padding:20px; background-color: red; color:white'";
    $min = preg_match("#[a-z]#", @$password);
    $maj = preg_match("#[A-Z]#", @$password);
    $num = preg_match("#[0-9]#", @$password);
    if (!preg_match("#^[a-zA-Z0-9ÄÅÇÉÑÖÜÂÊÁÀËÈÍÎÏÌÓÔÒÚÛÙáàâäãåçéèêëíìîïñóòôöõúùûü \-]+$#", $nom)) {
        $nom = "";
        $message = "<p $style >Nom invalide! Pas de Caractére special</p>";
    } elseif (!preg_match("#^[a-zA-Z0-9ÄÅÇÉÑÖÜÂÊÁÀËÈÍÎÏÌÓÔÒÚÛÙáàâäãåçéèêëíìîïñóòôöõúùûü \-]+$#", $prenom)) {
        $prenom = "";
        $message = "<p $style >Prénom invalide!Pas de Caractére special</p>";
    } elseif (!preg_match("#^[a-zA-Z0-96_.]+@[a-zA-Z0-96_.]+.[a-zA-Z]{2,6}$#", $email)) {
        $email = "";
        $message = "<p $style > Form de email invalide</p>";
    } elseif ($tab_email) {
        $message = "<p $style >Ce email est deja utiliser choisir un autre login </p>";
    } elseif (!preg_match("#[0-9]#", @$numero)) {
        $numero = "";
        $message = "<p $style >Numero invalide</p>";
    } elseif ($tab_num) {
        $message = "<p $style >Ce numero est deja utiliser choisir un autre numero </p>";
    } elseif ($min + $maj + $num != 3)
        $message = "<p $style >Mot de passe invalide! 1 maj 1 min 1 num</p>";
    else {
        try {
            //Inserting information into database
            $password_hash = md5($password);
            $sqlInsert = "INSERT INTO users (nom, email, numero, password, join_date) VALUES (:nom, :email, :numero, :password, NOW())";
            $statement = $db->prepare($sqlInsert);

            // Bind the values to placeholders
            $statement->bindParam(':nom', $nom);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':numero', $numero);
            $statement->bindParam(':password', $password_hash);

            // Execute the statement
            $statement->execute();

            if ($statement->rowCount() == 1) {
                $style = "style='padding:20px; background-color: #27AE60; color:white'";
                $message = "<p $style>Registration Successful, try to login</p>";

                $redirectURL = "login.php?email=" . urlencode($email) . "&password=" . urlencode($password_hash) . "&valider=true";
                header('Location: ' . $redirectURL);
                exit();

            }
        } catch (PDOException $ex) {
            $message = "error : <p $style" . $ex->getMessage() . "</p>";
        }
    }
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
    <link rel="stylesheet" href="../assets/css/style_form.css?t=<?= time() ?>">
    <title>Document</title>
</head>

<body>
    <div class="register">
        <div class="left">
            <a href="../index.php"><img src="../assets/images/logo.png" alt=""></a>
        </div>
        <div class="right">
            <form method="post">
                <h1>Register</h1>
                <p>The perfect Opportunitie to login blala</p>

                <!-- erreur_display -->
                <?php if (!empty($message)) { ?>
                    <div id="erreur">
                        <?= $message ?>
                    </div>
                <?php } ?>


                <div class="fullname">
                    <input type="text" name="nom" placeholder="Nom" value="<?= $nom ?>">
                    <input type="text" name="prenom" placeholder="Prenom" value="<?= $prenom ?>">
                </div>
                <input type="text" name="email" placeholder="Email" value="<?= $email ?>">
                <input type="text" name="numero" placeholder="Numero de telephone" value="<?= $numero ?>">
                <div id="passwordDiv">
                    <input type="password" name="password" id="password" placeholder="Password">
                    <i id="eye" class="fa-regular fa-eye-slash" onClick="Change()"></i>
                </div>

                <p id="passwordMessage" class="password-message">Your password is still invalide ...! (1 maj 1 min 1
                    num)</p>
                <input type="submit" name="submit" value="S'inscrire">
                <div class="or_section">
                    <div class="line"></div>
                    <div class="or">
                        <p>You already have an account ? <a href="login.php">Log-In</a></p>
                    </div>
                    <div class="line"></div>
                </div>
            </form>
        </div>
    </div>

    <script>

        let masque = true;
        let eye = document.getElementById('eye')
        function Change() {
            if (masque === true) {
                document.getElementById('password').setAttribute("type", "text");
                eye.classList.remove('fa-eye-slash')
                eye.classList.add('fa-eye')
                masque = false;
            } else {
                document.getElementById('password').setAttribute("type", "password");
                eye.classList.remove('fa-eye')
                eye.classList.add('fa-eye-slash')
                masque = true;
            }
        }

        const passwordInput = document.getElementById("password");
        const passwordMessage = document.getElementById("passwordMessage");

        passwordInput.addEventListener("input", validatePassword);
        // Initialize state on page load
        if (passwordInput.value === "") {
            passwordInput.style.borderColor = "";
            passwordInput.style.backgroundColor = "";
            passwordMessage.style.display = "none";
        } else {
            validatePassword();
        }

        function validatePassword() {
            const password = passwordInput.value;

            if (password === "") {
                // Hide error message and reset styles
                passwordInput.style.borderColor = "";
                passwordInput.style.backgroundColor = "";
                passwordMessage.style.display = "none";
            } else {
                const hasLowerCase = /[a-z]/.test(password);
                const hasUpperCase = /[A-Z]/.test(password);
                const hasNumber = /[0-9]/.test(password);

                if (hasLowerCase && hasUpperCase && hasNumber) {
                    passwordInput.style.borderColor = ""; // Reset border color
                    passwordInput.style.backgroundColor = ""; // Reset background color
                    passwordMessage.style.display = "none";
                } else {
                    passwordInput.style.borderColor = "red";
                    passwordInput.style.backgroundColor = "#ffcccc";
                    passwordMessage.style.color = "red"
                    passwordMessage.style.display = "block";
                }
            }
        }
    </script>

</body>

</html>