<?php
    function sanitize_input($input) {
        // Remove leading and trailing spaces
        $input = trim($input);

        // Remove potentially harmful characters
        $input = stripslashes($input);

        // Convert special characters to their HTML entities
        $input = htmlspecialchars($input);

        return $input;
};

function disanitize_input($input) {
    return htmlspecialchars_decode($input, ENT_QUOTES | ENT_HTML5);
}

?>