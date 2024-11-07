<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Order!</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .thank-you-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            color: #666;
            text-align: center;
        }

        .contact-support {
            margin-top: 30px;
            text-align: center;
        }

        .contact-support a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="thank-you-container">
        <h1>Thank You for Your Order!</h1>
        <p>Your order has been successfully placed. We're processing it and will update you shortly.</p>
        <p>For any inquiries or assistance, please don't hesitate to <a href="contact.html">contact our support
                team</a>.</p>

        <!-- Additional order details or information can be included here -->

        <div class="contact-support">
            <a href="index.php" class="button">Continue Shopping</a>
        </div>
        <?php if(isset($_SESSION["Connected"]) && $_SESSION["Connected"] == 1) { ?>
            <div class="Connected">
                <p>Since You created Your account You can track it in our <a href="profile.php">Profile Pannel</a></p>
                <div class="contact-support">
                    <a href="profile.php#tracking" class="button">Track my Order !</a>
                </div>
            </div>
        <?php } ?>
    </div>
</body>

</html>