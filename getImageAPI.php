<?php
//includes
include_once('includes/db_connection.php');
function getResizedImage($imagePath, $width, $height, $quality = 50)
{
    // Generate a unique identifier for the cached image based on the original image path and requested dimensions
    $cacheKey = md5($imagePath . $width . $height);

    // Define the cache directory where resized images are stored
    $cacheDirectory = 'cache/';

    // Check if the cached image exists
    $cachedImagePath = $cacheDirectory . $cacheKey . '.jpg';

    if (file_exists($cachedImagePath)) {
        // Serve the cached image
        header('Content-Type: image/jpeg');
        readfile($cachedImagePath);
    } else {
        // Create and store the resized image
        $image = imagecreatefromjpeg($imagePath);
        $resizedImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

        // Serve the resized image
        header('Content-Type: image/jpeg');
        imagejpeg($resizedImage, null, $quality);

        // Save the resized image to the cache
        imagejpeg($resizedImage, $cachedImagePath, $quality);

        // Clean up resources
        imagedestroy($image);
        imagedestroy($resizedImage);
    }
}

if (isset($_GET["id"]) && isset($_GET["image_type"])) {
    $id = $_GET["id"];
    $image_type = $_GET["image_type"];
    if (isset($_GET['quality'])) {
        $quality = $_GET["quality"];
    }
    try {
        if ($image_type != 'additional_image') {
            $sql = "SELECT $image_type FROM products WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $image = $stmt->fetchColumn();
                if ($image) {
                    if(isset($quality)){
                        getResizedImage($image,'1000','1000', $quality);
                    }else{
                        getResizedImage($image, '500', '500'); // Echo the result here
                    }
                } else {
                    echo "Image not found";
                }
            }
        } elseif ($image_type == 'additional_image') {
            $sql = "SELECT photo_url FROM product_photos WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $image = $stmt->fetchColumn();
                if ($image) {
                    if(isset($quality)){
                        getResizedImage($image,'1000','1000', $quality);
                    }else{
                        getResizedImage($image, '500', '500'); // Echo the result here
                    }
                } else {
                    echo "Image not found";
                }
            } else {
                echo "Problem in Select Query";
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>