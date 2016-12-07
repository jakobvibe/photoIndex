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
	<link href="js/vendor/photoswipe/photoswipe.css" rel="stylesheet"/>
	<link href="js/vendor/photoswipe/default-skin/default-skin.css" rel="stylesheet"/>
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
	
	<!-- Root element of PhotoSwipe. Must have class pswp. -->
	<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

		<!-- Background of PhotoSwipe. 
			 It's a separate element as animating opacity is faster than rgba(). -->
		<div class="pswp__bg"></div>

		<!-- Slides wrapper with overflow:hidden. -->
		<div class="pswp__scroll-wrap">

			<!-- Container that holds slides. 
				PhotoSwipe keeps only 3 of them in the DOM to save memory.
				Don't modify these 3 pswp__item elements, data is added later on. -->
			<div class="pswp__container">
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
			</div>

			<!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
			<div class="pswp__ui pswp__ui--hidden">

				<div class="pswp__top-bar">

					<!--  Controls are self-explanatory. Order can be changed. -->

					<div class="pswp__counter"></div>

					<button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

					<button class="pswp__button pswp__button--share" title="Share"></button>

					<button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

					<button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

					<!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->
					<!-- element will get class pswp__preloader--active when preloader is running -->
					<div class="pswp__preloader">
						<div class="pswp__preloader__icn">
						  <div class="pswp__preloader__cut">
							<div class="pswp__preloader__donut"></div>
						  </div>
						</div>
					</div>
				</div>

				<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
					<div class="pswp__share-tooltip"></div> 
				</div>

				<button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
				</button>

				<button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
				</button>

				<div class="pswp__caption">
					<div class="pswp__caption__center"></div>
				</div>

			</div>

		</div>
		<div class="photo-info">
			<ul>
				<li class="description title"><i class="icon icon-book"></i><span class="title">Beskrivelse</span></li>
				<li class="description text"></li>
				<li class="file-info title"><i class="icon icon-image6"></i><span class="title">Fil</span></li>
				<li class="file-info text">
					<span class="filename lead"></span>
					<span class="megapixel mini"></span><span class="dimensions mini"></span><span class="filesize mini"></span>
				</li>
				<li class="camera-info title"><i class="icon icon-camera2"></i><span class="title">Kamera</span></li>
				<li class="camera-info text">
					<span class="model lead"></span>
					<span class="aperture mini"></span><span class="exposure mini"></span><span class="iso mini"></span>
				</li>
				<li class="author-info title"><i class="icon icon-user"></i><span class="title">Fotograf</span></li>
				<li class="author-info text">
					<span class="author lead"></span>
					<span class="copyright mini"></span>
				</li>
				<li class="keywords title"><i class="icon icon-search3"></i><span class="title">Nøgleord</span></li>
				<li class="keywords text"></li>
			</ul>
		</div>

	</div>

	<?php include 'includes/scripts.php';?>
	<!-- Core JS file -->
	<script src="js/vendor/photoswipe/photoswipe.min.js"></script> 

	<!-- UI JS file -->
	<script src="js/vendor/photoswipe/photoswipe-ui-default.min.js"></script> 
	<script src="js/filebrowser.js"></script>

</body>
</html>