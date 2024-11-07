<?php
if (isset($_SESSION['id'])) {
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
}

try {
    $sql = "SELECT c.id, c.$user_id_column, c.product_id, c.quantity, c.size, c.style, c.color, p.image_principale, p.name, p.sale_price
        FROM $cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.$user_id_column = :user_id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id_value);
    $stmt->execute();
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo 'error' . $ex->getMessage();
}

// update_quantities.php
if (isset($_POST['Terminer'])) {
    // Loop through the submitted quantities and update the database
    echo var_dump($_POST);
    header('location:checkouts.php');
}

?>
<div class="card_pannel_screen_blur" id="blur_active">
    <div id="card_pannel" class="card_pannel">
        <h1>Pannier</h1>
        <div class="line"></div>

        <?php if (!isset($_SESSION['id'])) { ?>
            <p>Please Connect</p>
        <?php } elseif (count($cards) === 0) { ?>
            <p>Your Card is empty</p>
        <?php } else { ?>
            <form id="cart-form" action="index.php" method="post">

                <script>
                    function updateItemPrice(cardId, salePrice) {
                        const quantityInput = document.getElementById(`quantity-${cardId}`);
                        const itemPrice = document.getElementById(`itemPrice-${cardId}`);

                        // Get the quantity and sale price for the item
                        const quantity = parseInt(quantityInput.value);

                        // Calculate the new item price
                        const newPrice = (quantity * salePrice).toFixed(2); // Round to 2 decimal places

                        // Update the item price on the page
                        itemPrice.textContent = newPrice;
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
                                        console.log(xhr.responseText);
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
                                                        totalPrice.textContent = response.sum;
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
                                                const line = document.getElementById("line");
                                                line.remove();
                                                if (response.sum !== null) {
                                                    // Update the total price
                                                    var totalPrice = document.getElementById('total-price');
                                                    if (totalPrice) {
                                                        totalPrice.textContent = response.sum;
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

                                            } else {
                                                console.log(xhr)
                                                alert('Error ' + response.error)
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
                <div class="commande">
                    <div class="itemCheck">
                        <?php foreach ($cards as $item) {
                            $productTotal = $item["quantity"] * $item["sale_price"];
                            // $totalPrice += $productTotal;
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
                    <div class="prix_total" id="prixSumNotNull">
                        <div class="prix_display">
                            <p>Prix Total</p>
                            <div style="display: flex;">
                                <p id="total-price">0</p>
                                <p>Dhs</p>
                            </div>
                        </div>
                        <input type="submit" name="Terminer" value="Terminer la livraison">
                    </div>

                    <div class="prix_total" id="prixSumNull" style="display: none;">
                        <p>Your cart is empty</p>
                    </div>


            </form>
        </div>
    <?php } ?>
</div>