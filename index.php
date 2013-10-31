<?php
	// Load chapter list
	$navitems = explode("\n", file_get_contents("content/index"));
	$navitemTitles = array();
	for ($i = 0; $i < count($navitems); $i++) {
		$navitems[$i] = explode("/", $navitems[$i]);
		$navitemTitles[$navitems[$i][0]] = $navitems[$i][1];
	}

	// Requested content
	$content = $navitems[0][0];
	if (isset($_GET["content"])) $content = $_GET["content"];

	// Determine how to load the requested content
	$notfound = !preg_match("/^[a-z]+$/", $content) || !file_exists("content/" . $content . ".md");
	if ($notfound) {
		$contentFile = "content/notfound.md";
		$contentTitle = "Segmentation fault";
	} else {
		$contentFile = "content/" . $content . ".md";
		$contentTitle = $navitemTitles[$content];
	}

	// Cache mechanism
	$last_modified_time = gmdate("r", filemtime($contentFile)) . " GMT";
	$etag = md5_file($contentFile);
	
	if ((isset($_SERVER["HTTP_IF_MODIFIED_SINCE"]) && $_SERVER["HTTP_IF_MODIFIED_SINCE"] == $last_modified_time) ||
		(isset($_SERVER["HTTP_IF_NONE_MATCH"]) && str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $etag))
	{
		header("HTTP/1.1 304 Not Modified");
		exit;
	}

	header("ETag: " . $etag);
	header("Last-Modified: " . $last_modified_time);
	header("Cache-Control: public");
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>OpenGL - <?php print($contentTitle); ?></title>
		
		<meta name="description" content="An extensive, yet beginner friendly guide to using modern OpenGL for game development on all major platforms." />
		<meta name="author" content="Alexander Overvoorde" />
		<meta name="keywords" content="opengl, opengl 3.2, deprecated, non-deprecated, tutorial, guide, cross-platform, game, games, graphics, sfml, sdl, glfw, glut, openglut, beginner, easy" />
		<meta name="language" content="english" />
		
		<link rel="shortcut icon" type="image/png" href="media/tag.png" />
		<link rel="stylesheet" type="text/css" href="media/stylesheet.css" />
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
		<link rel="stylesheet" type="text/css" href="media/mobile.css" media="screen and (max-width: 1024px)" />

		<script type="text/x-mathjax-config">
			// MathJax
			MathJax.Hub.Config( {
			  tex2jax: { inlineMath: [ [ '$', '$' ], [ '\\(', '\\)' ] ] },
			  menuSettings: { context: "Browser" }
			} );
		</script>
		<script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
		
		<link rel="stylesheet" href="includes/zenburn.min.css" />
		<script type="text/javascript" src="http://yandex.st/highlightjs/6.1/highlight.min.js"></script>
		<script type="text/javascript" src="includes/glmatrix.js"></script>
		<script type="text/javascript">
			// Syntax highlighting
			hljs.initHighlightingOnLoad();
			
			// Disqus
			var disqus_url = "http://open.gl/?content=<?php print( $content ); ?>";
			var disqus_identifier = "<?php print( $content ); ?>";
			
			// Google Analytics
			var _gaq = _gaq || [];
			_gaq.push(["_setAccount", "UA-25119105-1"]);
			_gaq.push(["_setDomainName", "open.gl"]);
			_gaq.push(["_setAllowHash", "false"]);
			_gaq.push(["_trackPageview"]);

			(function()
			{
				var ga = document.createElement("script");
				ga.type = "text/javascript";
				ga.async = true;
				ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
				var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
			})();

			// WebGL demos
			var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame;
			var callbacks = [];
			var frame = function() {
				var time = +new Date() / 1000;

				for (var i = 0; i < callbacks.length; i++) {
					var rect = callbacks[i].canvas.getBoundingClientRect();

					if (rect.bottom > 0 && rect.top < window.innerHeight)
						callbacks[i].callback( time );
				}

				requestAnimationFrame(frame);
			}
			function registerAnimatedCanvas(canvas, callback) {
				callbacks.push({canvas: canvas, callback: callback});
			}
			requestAnimationFrame(frame);
		</script>
	</head>
	
	<body>
		<div id="page">
			<!-- Work in progress ribbon -->
			<a href="https://github.com/Overv/Open.GL"><img id="ribbon" src="media/ribbon_fork.png" alt="Fork me!" /></a>
			
			<!-- Content container -->
			<div id="content">
				<?php
					include_once("includes/markdown.php");
					
					print(Markdown(file_get_contents($contentFile)));
				?>
			</div>
			
			<hr />
			
			<!-- Navigation items -->
			<div id="nav">
				<ul>
					<?php
						foreach ($navitems as $navitem)
						{
							if ($navitem[0] == $content)
								print( '<li class="selected">' . $navitem[1] . '</li>' . "\n" );
							else
								print( '<li><a href="/' . $navitem[0] . '">' . $navitem[1] . '</a></li>' . "\n" );
						}
					?>
				</ul>
			</div>
			
			<?php
				if (!$notfound)
				{
			?>
			<hr />
			
			<!-- Disqus comments -->
			<div id="disqus_thread"></div>
			<script type="text/javascript">
				var dsq = document.createElement("script");
				dsq.type = "text/javascript";
				dsq.async = true;
				dsq.src = "http://opengl.disqus.com/embed.js";
				document.getElementsByTagName("head")[0].appendChild( dsq );
			</script>
			<?php
				}
			?>
		</div>
	</body>
</html>