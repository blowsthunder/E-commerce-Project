<?php
    session_start();
    //includes
    include_once('../includes/db_connection.php');

    //redirection
    if(isset($_POST["register"])){
        header("Location: register.php");
        exit;
    }

    if(isset($_GET["email"]) || isset($_GET['password']) || isset($_GET['valider'])){
        $email =$_GET['email'];
        $password =$_GET['password'];
        $_POST['valider'] = true;
    }
    //Code
    if (isset($_POST["valider"])) {
        !isset($email) ? $email = $_POST["email"] : '';
        !isset($password) ? $password = md5($_POST["password"]) : '';

        $style="style='padding:20px; background-color: red; color:white'";
    
        // Prepare and execute SQL statement
        $sel = $db->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND is_guest = 0 LIMIT 1");
        $sel->execute(array($email, $password));

        if ($sel) { // SQL statement executed successfully
            $tab = $sel->fetch(PDO::FETCH_ASSOC);
    
            if (!$tab) { // No user found with provided login and password
                $message = "<p $style>Mauvais login ou mot de passe!</p>";
            }
            else { // User authenticated successfully
                $_SESSION["id"] = $tab["id"];
                $_SESSION["Connected"] = 1;
                if(isset($_GET['Ordered']) && ($_GET['Ordered'] == true)){
                    header("Location: ../thankyou.php");
                }else{
                    header("Location: ../index.php");
                }

                exit;
            }
        } else { // SQL statement execution failed
            $message = "Une erreur s'est produite lors de la connexion.";
            error_log(print_r($db->errorInfo(), true)); // Log the error to the PHP error log
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style_form.css?t=<?=time()?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
</head>
<body>
    <div class="login_form">
        <div class="login_left">
            <a href="../index.php"><img src="../assets/images/logo.png" alt=""></a>
        </div>
        <div class="login_right">
            <form method="post">
            <h1>Login</h1>
            <p>Login_in to benefit from The lastest Promo and blala</p>

            <!-- erreur_display -->
            <?php if(!empty($message)){ ?>
            <div id="erreur">
            <?=$message?></div><?php } ?>

            <input type="text" name="email" placeholder="Email">
            <div id="passwordDiv">
                <input type="password" id="inputPassword" name="password" placeholder="Password">
                <i id="eye" class="fa-regular fa-eye-slash" onClick="Change()"></i>
            </div>
            <input type="submit" name="valider" value="Valider">
            
            <!-- forgot password -->
            <div class="or"><p>Forgot you password ? <a href="forgotPassword.php">Click-Here</a></p></div>

            <!-- or section -->
            <div class="or_section">
                <div class="line"></div>
                    <div class="or"><p>or</p></div>
                <div class="line"></div>
            </div>
            <!-- Register Section -->
            <input type="submit" name="register" value="Register" href="register.php">
        </form>
    </div>
    </div>
    <script>
        let masque = true;
        let eye = document.getElementById('eye')
        function Change(){
            if( masque === true){
                document.getElementById('inputPassword').setAttribute("type","text");
                eye.classList.remove('fa-eye-slash')
                eye.classList.add('fa-eye')
                masque=false;
            }else{
                document.getElementById('inputPassword').setAttribute("type","password");
                eye.classList.remove('fa-eye')
                eye.classList.add('fa-eye-slash')
                masque=true;
            }
        }
    </script>
</body>
</html>