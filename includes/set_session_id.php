<?php
// Check if $_SESSION['id'] is not set
if (!isset($_SESSION['id'])) {
    // Set $_SESSION['id'] to the value of PHPSESSID
    $_SESSION['id'] = session_id();
}

?>
