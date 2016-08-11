<!--@see http://tutorialzine.com/2014/09/cute-file-browser-jquery-ajax-php/ -->
<!DOCTYPE html>
<html>
<head lang="da">
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>photoIndex</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="shortcut icon" href="/favicon.ico?v=2" type="image/x-icon">
	
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,900,700italic,900italic' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Pirata+One' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/icons/style.css">
	<link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css" crossorigin="anonymous">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/styles/agate.min.css" media="screen">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/styles/vs.min.css" media="print">
	<link rel="stylesheet" href="js/vendor/jquery.minicolors.css">
	<link rel="stylesheet" href="css/style.php/main.scss">

	<!--[if lt IE 9]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
	<![endif]-->
	<link href="css/filebrowser.css" rel="stylesheet"/>

</head>
<body>
		<div class="container">
			<div class="main">
				<div class="filemanager">

					<div class="search">
						<input type="search" placeholder="Find en fil..." />
						<i class="icon icon-search3"></i>
					</div>

					<div class="breadcrumbs"></div>

					<ul class="data"></ul>

					<div class="nothingfound">
						<div class="nofiles"></div>
						<span>Splitte mine bramsejl!<br>Fil overbord!</span>
					</div>

				</div>
			</div>
		</div>

	<?php include 'includes/scripts.php';?>
	<script src="js/filebrowser.js"></script>

</body>
</html>