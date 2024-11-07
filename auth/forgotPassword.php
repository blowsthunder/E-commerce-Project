<?php 
//includes
include_once('../includes/db_connection.php');
include_once('../includes/form.php');
//variables
$message="";
$verify_token=md5(rand());
//Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    //Load Composer's autoloader
    require '../vendor/autoload.php';
    
    // Function to send verification email

        function send_email_verification($email,$verify_token) {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'generator.lead.100@gmail.com';
            $mail->Password = 'jwypvwttnvvvpenr';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->setFrom('generator.lead.100@gmail.com', 'Yasser');
            $mail->addAddress($email);
            $mail->Subject = 'Reset Passowrd';
            $mail->Body = "Please click on this link to verify your Reset your password address: http://localhost:8080/vuk%20wear/auth/resetPassword.php?token=$verify_token&email=$email";
            if(!$mail->send()) {
                $erreur = 'Message could not be sent.';
                $erreur .= 'Mailer Error: ' . $mail->ErrorInfo;
                return $erreur;
            }
            else {
                return 'Message has been sent';
            }
    }

    if(isset($_POST["valider"])){
        $style="style='padding:20px; background-color: red; color:white'";
        $sel = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $sel->execute(array($email));
        if ($sel) { // SQL statement executed successfully
            $tab = $sel->fetch(PDO::FETCH_ASSOC);
    
            if (!$tab) { // No user found with provided login and password
                $message = "<p $style>Email Doesn't Exist in our database!</p>";
            }
            else { 
                
                
                //Email Found
                $style="style='padding:20px; background-color: green; color:white'";

                //Insert a Token and The Token experty
                $expiry_time = time() + 3600; // Set token to expire in 1 hour
                $update_token_sql = "UPDATE users SET verify_token = :verify_token, expiry_time = :expiry_time WHERE email = :email";
                $statement = $db->prepare($update_token_sql);
                $statement->execute(array(':verify_token' => $verify_token, ':expiry_time' => $expiry_time, ':email' => $email));

                //send The email
                send_email_verification($email,$verify_token);
                $message = "<p $style>The email Has been Sent succesfully please check Your email</p>";
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
    <title>Document</title>
</head>
<body>
<body>
    <div class="login_form">
        <div class="login_left">
            <a href="../index.php"><img src="../assets/images/logo.png" alt=""></a>
        </div>
        <div class="login_right">
            <form method="post">
            <h1>Forgot Password</h1>
            <p>Enter Your Email to reset The password</p>

            <!-- erreur_display -->
            <?php if(!empty($message)){ ?>
            <div id="erreur">
            <?=$message?></div><?php } ?>

            <input type="text" name="email" placeholder="Email">
            <input type="submit" name="valider" value="Send an email">

            <!-- or section -->
            <div class="or_section">
                <div class="line"></div>
                    <div class="or"><a href="login.php" style="text-decoration :none;"><p>Go Back to Login</p></a></div>
                <div class="line"></div>
            </div>
        </form>
    </div>
    </div>
</body>
</html>