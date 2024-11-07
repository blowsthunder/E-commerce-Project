<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>A Simple Admin Dashboard</title>

	<script>
		// Check local storage
		let localS = localStorage.getItem('theme'),
			themeToSet = localS

		// If local storage is not set, we check the OS preference
		if (!localS) {
			themeToSet = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
		}

		// Set the correct theme
		document.documentElement.setAttribute('data-theme', themeToSet)
	</script>

	<!-- Reset -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/modern-normalize@1.1.0/modern-normalize.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!-- Google fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;700&display=swap" rel="stylesheet">

	<!-- Main stylesheet -->
	<link rel="stylesheet" href="../app.css?t=<?=time()?>">
	<link rel="stylesheet" href="style.css?t=<?=time()?>">
</head>