<?php
    //includes
    include_once('../includes/db_connection.php');

    //variables
    $message="";
    //Code
    if(isset($_GET['token'])){
        $token = $_GET['token']; // Get the token from the URL
    }else{$token="";}

    if(isset($_GET['email'])){
        $email=$_GET['email'];
    }else{$email="";}
    $now = time();

    $style="style='padding:20px; background-color: red; color:white'";

    if(!$token || !$email ){
        $message="<p $style>No token has been provided please try to reset password again .</p>";
    }

    // Retrieve token and expiry time from the database using the user's email
    $select_token_sql = "SELECT verify_token, expiry_time FROM users WHERE email = :email";
    $statement = $db->prepare($select_token_sql);
    $statement->execute(array(':email' => $email));
    $user_data = $statement->fetch(PDO::FETCH_ASSOC);

    if($now >= $user_data['expiry_time']){
        $message="<p $style>The token has expired please try to reset your password again</p>";
    }elseif(isset($_POST["valider"])){
    $newpassword= md5($_POST["newPassword"]);

    if ($user_data && $token === $user_data['verify_token'] && $now <= $user_data['expiry_time']) {
        // Token is valid and not expired, allow password reset
        // You can show the password reset form here
        $sqlQuery="UPDATE users SET password = :newpassword WHERE email = :email";
        $statement = $db->prepare($sqlQuery);
        $statement->execute(array(':newpassword' => $newpassword,':email' => $email));
        if($statement){
            $style="style='padding:20px; background-color: green; color:white'";
            $message="<p $style>Your Password Has been reset succesfully</p>";
        }else{
            $message="<p $style>A error has accured please try again</p>";
        }
    } else {
        // Token is invalid or expired, show an error message
        $message = "Your token was Invalid";
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
    <div class="login_form">
        <div class="login_left">
            <a href="../index.php"><img src="../assets/images/logo.png" alt=""></a>
        </div>
        <div class="login_right">
        <form method="post">

                <h1>Reset Your Password</h1>
                <p>Please Enter Your new Password</p>

            <!-- erreur_display -->
            <?php if(!empty($message)){ ?>
            <div id="erreur">
            <?=$message?></div><?php } ?>
            <div class="<?= (!empty($message)) ? "hidden" : ""; ?>">
                <input type="password" name="newPassword" id="newPassword" placeholder="New Password">
                <p id="passwordMessage" class="password-message">Password must contain at least 1 uppercase letter, 1 lowercase letter, and 1 number</p>
                <input type="submit" id="submitButton" name="valider" value="Reset Password" disabled>
            </div>


        </form>
    </div>
    </div>

    

<script>
    const newPasswordInput = document.getElementById("newPassword");
    const passwordMessage = document.getElementById("passwordMessage");
    const submitButton = document.getElementById("submitButton");

    newPasswordInput.addEventListener("input", validatePassword);

    if(newPasswordInput.value===""){
        newPasswordInput.style.borderColor = ""; // Reset border color
        newPasswordInput.style.backgroundColor = ""; // Reset background color
        passwordMessage.style.display = "none";
        submitButton.style.cursor = "no-drop";
    }


    function validatePassword() {
    const password = newPasswordInput.value;
    
    const hasLowerCase = /[a-z]/.test(password);
    const hasUpperCase = /[A-Z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    
    if (hasLowerCase && hasUpperCase && hasNumber) {
        newPasswordInput.style.borderColor = ""; // Reset border color
        newPasswordInput.style.backgroundColor = ""; // Reset background color
        passwordMessage.style.display = "none";
        submitButton.disabled = false;
        submitButton.style.cursor = "pointer";
        return true; // Password is valid
    } else {
        newPasswordInput.style.borderColor = "red";
        newPasswordInput.style.backgroundColor = "#ffcccc";
        passwordMessage.style.display = "block";
        passwordMessage.style.color="red";
        submitButton.style.cursor = "no-drop";
        return false; // Password is invalid
    }
}

    </script>

</body>
</html>