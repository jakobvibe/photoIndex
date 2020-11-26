<!DOCTYPE html>
<html>
<head lang="da">
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>SL2017 Foto galleri</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="shortcut icon" href="/favicon.ico?v=2" type="image/x-icon">
	
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,900,700italic,900italic' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/icons/style.css">
	<link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css" crossorigin="anonymous">
	<link href="css/filebrowser.css" rel="stylesheet"/>
	<link href="js/vendor/photoswipe/photoswipe.css" rel="stylesheet"/>
	<link href="js/vendor/photoswipe/default-skin/default-skin.css" rel="stylesheet"/>
	<link rel="stylesheet" href="css/style.php/main.scss?v=<?php echo filemtime('css/scss/main.scss')?>">

	<!--[if lt IE 9]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
	<![endif]-->

</head>
<?php

if (isset($_REQUEST['view'])) {
	$bodyclass = 'folder';
} else {
	$bodyclass = 'frontpage';
}

?>
<body class="<?php echo $bodyclass; ?>">
<div class="container">
	<div class="logo"><a href="http://foto.sl2017.dk"><img class="image" src="images/sl2017logo.svg"></a></div>
	<div class="headerbar"><a href="http://foto.sl2017.dk">SL2017 Foto Galleri</a></div>
	<div class="subhead">Alt materiale på denne side er omfattet af ophavsret.
		<br>Alle billeder skal krediteres med fotografens navn ved brug og må kun bruges af Spejdernes Lejr 2017, samt Spejderkorps tilknyttet Foreningen Spejderne
		<br> og ved redaktionel omtale af Spejdernes Lejr 2017, samt disse spejderkorps.
		<br>Har du spørgsmål kontakt: <a href="mailto:foto@sl2017.dk">foto@sl2017.dk</a>
		<br><a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/"><img alt="Creative Commons licens" style="border-width:0" src="https://i.creativecommons.org/l/by-nc/4.0/88x31.png" /></a>
	</div>
	<div class="main">
		<div class="filemanager">
			<div class="controls">
<?php 
if (!isset($_REQUEST['view'])) {
	echo '<a href="?view=folder&path=photos" class="btn btn-primary nav-btn"><i class="icon icon-folder"></i> Mappevisning</a>';
} else {
	echo '<a href="/" class="btn btn-primary nav-btn"><i class="icon icon-home"></i> Forside</a>';
}
?>
				<div class="clear-search">
					<i class="icon icon-arrow-left"></i>
				</div>
				<div class="search">
					<input type="search" tabindex="0" autofocus placeholder="Find en fil..." />
					<i class="icon icon-search3"></i>
				</div>
			</div>	
					<div class="breadcrumbs"></div>
					<ul class="data"></ul>
					<?php

						if (!isset($_REQUEST['view'])) {
					?>
						<div>
							<?php 
								if (isset($_REQUEST['offset'])) {
									$offset = $_REQUEST['offset']+500;
								} else {
									$offset = 500;
								}

								if ($offset == 500) {
									echo "<a href=\"?offset={$offset}\" class=\"btn btn-primary load-next\"><i class=\"icon icon-arrow-down16\"></i> Indlæs næste side</a>";
								} else if ($offset > 500) {
									$prev_offset = $offset - 500;
									echo "<a href=\"?offset={$prev_offset}\"  class=\"btn btn-primary load-prev\"><i class=\"icon icon-arrow-up16\"></i> Indlæs forrige side</a>";
									echo "<a href=\"?offset={$offset}\" class=\"btn btn-primary load-next\"><i class=\"icon icon-arrow-down16\"></i> - Indlæs næste side</a>";
								}
							?>
							
						</div>
					<?php } ?>
					<div class="nothingfound">
						<div class="nofiles"></div>
						<span>Vær beredt!<br>Det lykkedes ikke her :(</span>
					</div>

				</div>
			</div>
		</div>
	
	<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="pswp__bg"></div>

		<div class="pswp__scroll-wrap">

			<div class="pswp__container">
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
			</div>

			<div class="pswp__ui pswp__ui--hidden">

				<div class="pswp__top-bar">


					<div class="pswp__counter"></div>

					<button class="pswp__button pswp__button--close" title="Luk (Esc)"></button>

					<button class="pswp__button pswp__button--share" title="Handlinger"></button>

					<button class="pswp__button pswp__button--fs" title="Fuldskærm"></button>

					<button class="pswp__button pswp__button--zoom" title="Zoom ind/ud"></button>

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
				<li class="copyright text"><a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/"><img alt="Creative Commons licens" style="border-width:0" src="https://i.creativecommons.org/l/by-nc/4.0/88x31.png" /></a></li>
			</ul>
			
		</div>

	</div>

	<?php include 'includes/scripts.php';?>
	<!-- Core JS file -->
	<script src="js/vendor/photoswipe/photoswipe.js"></script> 

	<!-- UI JS file -->
	<script src="js/vendor/photoswipe/photoswipe-ui-default.min.js"></script> 
	
	<?php 
        if (isset($_REQUEST['view']) && $_REQUEST['view'] == 'folder') { 
            if (isset($_REQUEST['path'])) {
                $path = "?path=".$_REQUEST['path'];
            }
            echo "<script>var dataFile = 'getDirList.php{$path}';</script>"; 
        } else if (isset($_REQUEST['view']) && $_REQUEST['view'] == 'search') {
            $path = "?q=".$_REQUEST['q'];
            echo "<script>var dataFile = 'getFullList.php{$path}';</script>"; 
        } else {
            $offset = $_REQUEST['offset'] ? "?offset={$_REQUEST['offset']}" : "";
            echo "<script>var foldersFile = 'getDirList.php?path=photos';"; 
            echo "var dataFile = 'getFullList.php".$offset."';</script>"; 
        }
    ?>
	
	<script src="js/filebrowser.js?v=<?php echo filemtime('js/filebrowser.js'); ?>"></script>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-102179569-1', 'auto');
		ga('send', 'pageview');

	</script>



	<!-- Piwik -->
	<script type="text/javascript">
	  var _paq = _paq || [];
	  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
	  _paq.push(['trackPageView']);
	  _paq.push(['enableLinkTracking']);
	  (function() {
	    var u="//foto.sl2017.dk/piwik/";
	    _paq.push(['setTrackerUrl', u+'piwik.php']);
	    _paq.push(['setSiteId', '1']);
	    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
	    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
	  })();
	</script>
	<noscript><p><img src="//foto.sl2017.dk/piwik/piwik.php?idsite=1&rec=1" style="border:0;" alt="" /></p></noscript>
	<!-- End Piwik Code -->


</body>
</html>
