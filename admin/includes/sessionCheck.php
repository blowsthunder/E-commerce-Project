<?php
	session_start();
	if(!isset($_SESSION["id"]) || $_SESSION["id"] != 1 ){
        http_response_code(404);
		echo "<!DOCTYPE html>
		<html lang=\"en\">
		<head>
			<meta charset=\"UTF-8\">
			<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
			<title>Document</title>
		</head>
		<body>
			<h1>Page introvable</h1>
		</body>
		</html>";
		exit();
	}
	
?>