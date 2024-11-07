<?php
session_start();
//include
include_once('includes/db_connection.php');
include_once('cities.php');
//Variable 
$totalPrice = 0;
$message = '';
// Check if the current user is a guest or registered user
if ($_SESSION['id'] === session_id()) {
    $cart = 'guest_cart';
    $user_id_column = 'session_id';
    $user_id_value = session_id(); // Use the current session ID for guest users
} else {
    $cart = 'cart';
    $user_id_column = 'user_id';
    $user_id_value = $_SESSION['id']; // Use the user's ID for registered users
}

try {
    $sql = "SELECT c.id, c.$user_id_column, c.product_id, c.quantity, c.size, c.style, c.color, p.image_principale, p.name, p.sale_price
        FROM $cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.$user_id_column = :user_id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id_value);
    $stmt->execute();
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo "Error : " . $ex->getMessage();
}
;

if ($cart === 'cart') {
    try {
        $sql = "SELECT nom,numero,numero from users where id= :user_id";
        $smt = $db->prepare($sql);
        $smt->bindParam(':user_id', $_SESSION['id']);
        $smt->execute();
        $userInfo = $smt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        echo "Error : " . $ex->getMessage();
    }
    ;
}

if (isset($_POST["valider"])) {
    $register = false;

    // Check if 'Register' checkbox is checked or 'Register' exists in POST and its value is "on"
    if (isset($_POST['RegisterInput']) && $_POST['RegisterInput'] === "1") {
        $register = true; // Set $register to true if the checkbox is checked
    }

    // Validate form data and prepare for insertion
    if (empty($_POST['user_name']) || empty($_POST['city']) || empty($_POST['adresse']) || empty($_POST['phone'])) {
        $message = "Please Right all feild";
    } elseif ($register == true and empty($_POST['email']) and empty($_POST['password'])) {
        $message = "Since Your Want to create an Account please write your Email and Password";
        echo $register;
    } else {
        // Insert into 'orders' table
        if ($_SESSION['id'] === session_id()) {
            if ($register == true) {
                $email = $_POST['email'];
                $password = md5($_POST['password']);
                $isGuest = 0;
            } else {
                $email = '-';
                $password = '-';
                $isGuest = 1;
            }
            try {
                $guestName = $_POST['user_name'];
                $joinDate = date("Y-m-d");
                $numero = $_POST['phone'];
                $verifyToken = md5(rand()); // You might generate this token for guest users
                $expiryTime = 0;

                $insertUserSql = "INSERT INTO users (nom,email,numero,password, join_date, verify_token, expiry_time, is_guest)
                              VALUES (:nom,:email, :numero ,:password, :join_date, :verify_token, :expiry_time, :is_guest)";

                $insertUserStatement = $db->prepare($insertUserSql);
                $insertUserStatement->bindParam(':nom', $guestName);
                $insertUserStatement->bindParam(':email', $email);
                $insertUserStatement->bindParam(':numero', $numero);
                $insertUserStatement->bindParam(':password', $password);
                $insertUserStatement->bindParam(':join_date', $joinDate);
                $insertUserStatement->bindParam(':verify_token', $verifyToken);
                $insertUserStatement->bindParam(':expiry_time', $expiryTime);
                $insertUserStatement->bindParam(':is_guest', $isGuest);
                $insertUserStatement->execute();

                // Get the user ID of the inserted guest user
                $guestUserId = $db->lastInsertId();
                $_SESSION['id'] = $guestUserId;
            } catch (PDOException $ex) {
                echo "Error: " . $ex->getMessage();
            }
        }

        try {
            $orderDate = date("Y-m-d H:i:s"); // Get current date and time
            $city = $_POST["city"];
            $street = $_POST["adresse"];
            // $totalAmount = $totalPrice; // Calculated total amount from the cart
            $statusId = 1; // You might need to define a suitable status ID

            $insertOrderSql = "INSERT INTO orders (user_id, order_date, city, street, status_id)
                               VALUES (:user_id, :order_date, :city, :street, :status_id)";

            $insertOrderStatement = $db->prepare($insertOrderSql);
            $insertOrderStatement->bindParam(':user_id', $_SESSION['id']);
            $insertOrderStatement->bindParam(':order_date', $orderDate);
            // $insertOrderStatement->bindParam(':total_amount', $totalAmount);
            $insertOrderStatement->bindParam(':city', $city);
            $insertOrderStatement->bindParam(':street', $street);
            $insertOrderStatement->bindParam(':status_id', $statusId);
            $insertOrderStatement->execute();

            $orderId = $db->lastInsertId(); // Get the ID of the inserted order

            // Insert into 'order_items' table
            foreach ($cartItems as $item) {
                $productId = $item["product_id"];
                $quantity = $item["quantity"];
                $subtotal = $item["sale_price"] * $quantity;
                $totalPrice += $subtotal;

                $insertOrderItemSql = "INSERT INTO order_items (order_id, product_id, quantity, subtotal)
                                       VALUES (:order_id, :product_id, :quantity, :subtotal)";

                $insertOrderItemStatement = $db->prepare($insertOrderItemSql);
                $insertOrderItemStatement->bindParam(':order_id', $orderId);
                $insertOrderItemStatement->bindParam(':product_id', $productId);
                $insertOrderItemStatement->bindParam(':quantity', $quantity);
                $insertOrderItemStatement->bindParam(':subtotal', $subtotal);
                if ($insertOrderItemStatement->execute()) {
                    // Insertion was successful
                    $sqlupdate = "UPDATE orders SET total_amount = :totalPrice WHERE id = :orderId";
                    $statement = $db->prepare($sqlupdate);
                    $statement->bindParam(':totalPrice', $totalPrice);
                    $statement->bindParam(':orderId', $orderId);
                    $statement->execute();

                    try {
                        $sql = "DELETE FROM cart WHERE user_id= :user_id";
                        $statetment = $db->prepare($sql);
                        $statetment->bindParam(":user_id", $_SESSION['id']);
                        $statetment->execute();
                        if ($register == true) {
                            $redirectURL = "auth/login.php?email=" . urlencode($email) . "&password=" . urlencode($password) . "&valider=true&Ordered=true";
                            header('Location: ' . $redirectURL);
                            exit();
                        } else {
                            header('location:thankyou.php');
                        }
                    } catch (PDOException $ex) {
                        echo "Error: " . $ex->getMessage();
                    }
                    // Redirect or display a success message
                    echo "Order placed successfully!";
                } else {
                    // Insertion failed
                    echo "Error placing the order.";
                }
            }
        } catch (PDOException $ex) {
            echo "Error: " . $ex->getMessage();
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style_payement.css?t=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
</head>

<script>
    function checkEmail(email) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', './Components/checkEmail.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    if (xhr.response = 1) {
                        var submitBtn = document.getElementById('submit').disabled = true;
                        var p = document.getElementById('message').innerHTML = "already used email, try Another Email"
                        console.log(xhr.response);
                    }
                }
            }
        }
        var data = 'email=' + email;
        xhr.send(data);
    }
    checkEmail('phylefreak@gmail.com');
</script>
<script>
    function updateItemPrice(cardId, salePrice) {
        const quantityInput = document.getElementById(`quantity-${cardId}`);
        const itemPrice = document.getElementById(`itemPrice-${cardId}`);

        // Get the quantity and sale price for the item
        const quantity = parseInt(quantityInput.value);

        // Calculate the new item price
        const newPrice = (quantity * salePrice).toFixed(2); // Round to 2 decimal places

        // Update the item price on the page
        itemPrice.textContent = 'total Price : ' + newPrice + ' Dhs';
    }


</script>
<script>
    //Price


    // Function to update cart item quantity
    function updateCartItem(id, action, salePrice) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', './Components/cardAPI.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.quantity !== undefined) {
                            // Check if the element with the specified ID exists
                            var inputElement = document.getElementById('quantity-' + id);
                            if (inputElement !== null && response.quantity !== 'DELETED') {
                                // Update the quantity in the DOM
                                var newQuantity = parseInt(response.quantity);
                                inputElement.value = newQuantity;
                                updateItemPrice(id, salePrice);
                                if (response.sum !== null) {
                                    // Update the total price
                                    var totalPrice = document.getElementById('total-price');
                                    if (totalPrice) {
                                        totalPrice.textContent = response.sum + ' Dhs';
                                    }

                                    // Show the container for a non-empty cart
                                    var prixSumNotNull = document.getElementById('prixSumNotNull');
                                    if (prixSumNotNull) {
                                        prixSumNotNull.style.display = 'block';
                                    }

                                    // Hide the container for an empty cart
                                    var prixSumNull = document.getElementById('prixSumNull');
                                    if (prixSumNull) {
                                        prixSumNull.style.display = 'none';
                                    }
                                } else {
                                    // Hide the container for a non-empty cart
                                    var prixSumNotNull = document.getElementById('prixSumNotNull');
                                    if (prixSumNotNull) {
                                        prixSumNotNull.style.display = 'none';
                                    }

                                    // Show the container for an empty cart
                                    var prixSumNull = document.getElementById('prixSumNull');
                                    if (prixSumNull) {
                                        prixSumNull.style.display = 'block';
                                    }
                                }

                            } else if (inputElement !== null && response.quantity == 'DELETED') {
                                const element = document.getElementById("product " + response.item);
                                element.remove();
                                if (response.sum !== null) {
                                    // Update the total price
                                    var totalPrice = document.getElementById('total-price');
                                    if (totalPrice) {
                                        totalPrice.textContent = response.sum + ' Dhs';
                                    }

                                    // Show the container for a non-empty cart
                                    var prixSumNotNull = document.getElementById('prixSumNotNull');
                                    if (prixSumNotNull) {
                                        prixSumNotNull.style.display = 'block';
                                    }

                                    // Hide the container for an empty cart
                                    var prixSumNull = document.getElementById('prixSumNull');
                                    if (prixSumNull) {
                                        prixSumNull.style.display = 'none';
                                    }
                                } else {
                                    // Hide the container for a non-empty cart
                                    var prixSumNotNull = document.getElementById('prixSumNotNull');
                                    if (prixSumNotNull) {
                                        prixSumNotNull.style.display = 'none';
                                    }

                                    // Show the container for an empty cart
                                    var prixSumNull = document.getElementById('prixSumNull');
                                    if (prixSumNull) {
                                        prixSumNull.style.display = 'block';
                                    }
                                }

                            }
                        } else {
                            alert('Error updating quantity: ' + response.error);
                        }
                    } catch (error) {
                        alert('Error parsing JSON: ' + error);
                    }
                } else {
                    alert('Error updating quantity.');
                }
            }
        };
        var data = 'id=' + id + '&action=' + action + '&cart=' + '<?php echo $cart; ?>' + '&user_id=' + '<?php echo $user_id_value ?>';
        xhr.send(data);

    }

</script>
<script>
    function DeleteItem(id) {
        const confirmbutton = document.getElementById('confirm');
        const blur = document.getElementById('blur');
        const yesButton = document.getElementById('yes');
        const noButton = document.getElementById('non');

        confirmbutton.classList.add('active');
        blur.addEventListener('click', () => {
            confirmbutton.classList.remove('active');
        });

        yesButton.addEventListener('click', () => {
            updateCartItem(id, 'delete');
            confirmbutton.classList.remove('active');
            document.getElementById('deleteBtn-' + id).remove;
        });

        noButton.addEventListener('click', () => {
            confirmbutton.classList.remove('active');
        })


    }
</script>

<body>
    <div class="confirmDelete" id="confirm">
        <div class="blur" id="blur">
        </div>
        <div class="message">
            <p>are you sure You want to Delete This Product</p>
            <div class="buttons">
                <input type="button" value="Yes" id="yes">
                <input type="button" value="Keep the item" id="non">
            </div>
        </div>
    </div>

    <div class="RegisterInfo" id="Register_info">
        <div class="blur" id="blur_register"></div>
        <div class="message">
            <p>Creating an account will give you the abilities to Track your Order in every stage intel iy get Shipped
            </p>
            <p>Your Information Does not get Collected an it will not be selled to other campanies</p>
            <p>Your Data are safe</p>
        </div>
    </div>
    <div class="user_information">
        <a href="index.php"><img src="assets/images/logo.png"></a>
        <form action="#" method="post" onsubmit="return validateForm()">
            <h4>Your Payement details</h4>
            <h6>Provide Your Payement details blow</h6>
            <?php if (!empty($message)) { ?>
                <div id="erreur">
                    <?= $message ?>
                </div>
            <?php } ?>

            <div class="fullname">
                <h3>Your name</h3>
                <input type="text" name="user_name" placeholder="Nom"
                    value="<?= isset($userInfo["nom"]) ? $userInfo["nom"] : '' ?>" <?= isset($userInfo["nom"]) ? 'readonly' : '' ?>>
            </div>

            <div class="city-select">
                <h3>Your city</h3>
                <select name="city" id="city-select">
                    <option value="" disabled selected>Choose a city</option>
                    <?php foreach ($cities as $city => $price) { ?>
                        <option value="<?= $city ?>" data-price="<?= $price ?>">
                            <?= $city ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="input">
                <h3>Your adresse</h3>
                <input type="text" name="adresse" placeholder="Adresse Complet">
            </div>

            <div class="input">
                <h3>Your number</h3>
                <input type="number" name="phone" placeholder="Numero de téléphone (obligatoire)"
                    value="<?= isset($userInfo["numero"]) ? $userInfo["numero"] : '' ?>">
            </div>
            <div class="check">
                <div>
                    <input type="checkbox" name="RegisterInput" id="Register_btn" value="1">
                    <label for="Register_btn">Want to create an account</label>
                </div>
                <p id="infoDisplay">Why Should i Create an account ?</p>
            </div>
            <div class="input" id="Register-email">
                <h3>Your Email</h3>
                <p id="message"></p>
                <div style="display:flex;justify-content:space-between">
                    <input type="text" name="email" placeholder="Email">
                    <i class="fa-regular fa-circle-check" style="font-size:16px;color:gray"></i>
                </div>
            </div>
            <div class="input" id="Register-pass">
                <h3>Your Password</h3>
                <input type="password" name="password" placeholder="Password">
            </div>
            <input id="submit" type="submit" name="valider" value="Finish the Order">
        </form>
    </div>
    <div class="commande">
        <h1>Your Order</h1>
        <h3>Review Your Order blow</h3>
        <div class="itemCheck">
            <?php foreach ($cartItems as $item) {
                $productTotal = $item["quantity"] * $item["sale_price"];
                $totalPrice += $productTotal;
                ?>
                <div class="product_Information" id="product <?= $item['id'] ?>">
                    <i class="fa-solid fa-xmark" onclick="DeleteItem(<?= $item['id'] ?>)"
                        id="deleteBtn-<?= $item['id'] ?>"></i>
                    <div class="image_product">
                        <img src="<?= $item["image_principale"] ?>">
                    </div>
                    <div class="info">
                        <p>
                            Name :
                            <?= $item["name"] ?>
                        </p>
                        <p>
                            Price :
                            <?= $item["sale_price"] ?>
                        </p>
                        <p id="itemPrice-<?php echo $item['id']; ?>">
                            total Price :
                            <?= $productTotal ?>.00 Dhs
                        </p>
                    </div>
                    <div class="quantity">
                        <input type="button" class="quantity-button"
                            onclick="updateCartItem(<?php echo $item['id']; ?>, 'add' , <?= $item['sale_price'] ?>)"
                            value="+">
                        <input type="number" name="quantity[<?php echo $item['product_id']; ?>]"
                            id="quantity-<?php echo $item['id']; ?>" value="<?php echo $item["quantity"]; ?>"
                            onchange="updateItemPrice(<?php echo $item['id']; ?>)" disabled>
                        <input type="button" class="quantity-button"
                            onclick="updateCartItem(<?php echo $item['id']; ?>, 'subtract' , <?= $item['sale_price'] ?>)"
                            value="-">
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="line"></div>
        <div class="total">
            <h4> Shipping Price: </h4>
            <h4 id="city-price" style="opacity:0.7">
                Select a city
            </h4>
        </div>
        <div class="total">
            <h4>Final Price : </h4>
            <h4 id="total-price">
                <?= $totalPrice ?>.00 Dhs
            </h4>
        </div>
    </div>
</body>

</html>
<script>
    function validateForm() {
        var userName = document.querySelector('input[name="user_name"]');
        var city = document.querySelector('input[name="city"]');
        var adresse = document.querySelector('input[name="adresse"]');
        var phone = document.querySelector('input[name="phone"]');
        var errorDiv = document.getElementById('erreur');
        var errorMessage = '';

        if (userName.value.trim() === '' || city.value.trim() === '' || adresse.value.trim() === '' || phone.value.trim() === '') {
            errorMessage = "Please fill out all fields.";
            errorDiv.textContent = errorMessage;
            return false; // Prevent form submission
        }

        errorDiv.textContent = ''; // Clear previous error messages
        return true; // Allow form submission
    }


    //Calculate The price ! 
    var citySelect = document.getElementById('city-select');
    var totalPrice = document.getElementById('total-price');
    var displayCityPrice = document.getElementById('city-price')
    var previousCityPrice = 0;

    citySelect.addEventListener("change", () => {
        var selectedOption = citySelect.options[citySelect.selectedIndex];
        // Get the price associated with the selected city
        var cityPrice = parseFloat(selectedOption.getAttribute("data-price"));
        displayCityPrice.textContent = '+' + cityPrice.toFixed(2) + ' Dhs';
        displayCityPrice.style.opacity = '1';


        // Subtract the previous city's price before adding the new city's price
        var newTotal = parseFloat(totalPrice.textContent) - previousCityPrice + cityPrice;

        // Update the total price
        totalPrice.textContent = newTotal.toFixed(2) + ' Dhs'; // Display the new total with 2 decimal places

        // Update the previous city's price for the next change
        previousCityPrice = cityPrice;
    });


    //Display info about Register
    const registerInfo = document.getElementById('infoDisplay');
    const info = document.getElementById('Register_info');
    const blur = document.getElementById('blur_register');

    registerInfo.addEventListener('click', () => {
        info.classList.add('active');
    })

    blur.addEventListener('click', () => {
        info.classList.remove('active')
    })


    //Display the additional fields
    const registerCheckbox = document.getElementById('Register_btn');
    const registerInputEmail = document.getElementById('Register-email');
    const registerInputPass = document.getElementById('Register-pass');

    registerCheckbox.addEventListener('change', function () {
        if (this.checked) {
            // Checkbox is checked - show the input fields
            registerInputEmail.classList.add('Active');
            registerInputPass.classList.add('Active');
        } else {
            // Checkbox is unchecked - hide the input fields
            registerInputEmail.classList.remove('Active');
            registerInputPass.classList.remove('Active');
        }
    });





</script>