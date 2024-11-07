<?php
require_once '../vendor/autoload.php'; // Include the Twig autoload file
include_once('../includes/sessionCheck.php');
include_once('../includes/db_connection.php');

// Create a new Twig environment
$loader = new \Twig\Loader\FilesystemLoader(__DIR__); // __DIR__ should be the directory where your template file is located
$twig = new \Twig\Environment($loader);

$id = $_POST['id'];

try {
    $sql = 'SELECT * FROM products WHERE id = :id';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $productInfo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo 'Error retrieving product information: ' . $ex->getMessage();
}

if ($productInfo) {
    // Render the template with productInfo data
    echo $twig->render('product_template.twig', ['productInfo' => $productInfo]);
} else {
    echo 'Product not found.';
}
?>
