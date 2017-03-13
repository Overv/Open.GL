<?php
	// Requested language
	$lang = 'en';
	$languages = explode("\n", file_get_contents("content/languages"));
	if (isset($_GET['lang']) && in_array($_GET['lang'], $languages)) {
		$lang = $_GET['lang'];
	}

	// Load chapter list
	$navitems = explode("\n", file_get_contents("content/articles-" . $lang . "/index"));
	$navitemTitles = array();
	for ($i = 0; $i < count($navitems); $i++) {
		$navitems[$i] = explode("/", $navitems[$i]);
		$navitemTitles[$navitems[$i][0]] = $navitems[$i][1];
	}

	// Requested content
	$content = $navitems[0][0];
	if (isset($_GET["content"])) $content = $_GET["content"];

	// Determine how to load the requested content
	$notfound = !preg_match("/^[a-z]+$/", $content) || !file_exists("content/articles-" . $lang . "/" . $content . ".md");
	if ($notfound) {
	        $content = 'notfound';
		$contentFile = "content/articles-" . $lang . "/notfound.md";
		$contentTitle = "Segmentation fault";
	} else {
		$contentFile = "content/articles-" . $lang . "/" . $content . ".md";
		$contentTitle = $navitemTitles[$content];
	}
	$contentSource = file_get_contents($contentFile);

	// Cache mechanism
	$last_modified_time = gmdate("r", max(filemtime('index.php'), filemtime($contentFile))) . " GMT";
	$etag = md5(file_get_contents('index.php') . $contentSource);

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
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">

		<title>OpenGL - <?php print($contentTitle); ?></title>

		<meta name="description" content="An extensive, yet beginner friendly guide to using modern OpenGL for game development on all major platforms." />
		<meta name="author" content="Alexander Overvoorde" />
		<meta name="keywords" content="opengl, opengl 3.2, deprecated, non-deprecated, tutorial, guide, cross-platform, game, games, graphics, sfml, sdl, glfw, glut, openglut, beginner, easy" />

		<link rel="shortcut icon" type="image/png" href="/media/tag.png" />
		<link rel="stylesheet" type="text/css" href="/media/stylesheet.css" />

		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
		<link rel="stylesheet" type="text/css" href="/media/mobile.css" media="screen and (max-width: 1024px)" />

		<script type="text/x-mathjax-config">
			// MathJax
			MathJax.Hub.Config( {
			  tex2jax: { inlineMath: [ [ '$', '$' ], [ '\\(', '\\)' ] ] },
			  menuSettings: { context: "Browser" }
			} );
		</script>
		<script type="text/javascript" src="//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>

		<link rel="stylesheet" href="/includes/zenburn.min.css" />
		<script type="text/javascript" src="//yandex.st/highlightjs/6.1/highlight.min.js"></script>
		<script type="text/javascript" src="/includes/glmatrix.js"></script>
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
		<!--[if lt IE 9]>
		<script src="media/html5shiv.js"></script>
		<![endif]-->
	</head>

	<body>
		<div id="page">
			<!-- Work in progress ribbon -->
			<a href="https://github.com/Overv/Open.GL"><img id="ribbon" src="/media/ribbon_fork.png" alt="Fork me!" /></a>

			<!-- Navigation items -->
			<input type="checkbox" id="nav_toggle" />
			<nav>
				<label for="nav_toggle" data-open="&#x2261;" data-close="&#x2715;"></label>
				<ul>
					<?php
						foreach ($navitems as $navitem)
						{
							if ($navitem[0] == $content)
								print( '<li class="selected">' . $navitem[1] . '</li>' . "\n" );
							else
								print( '<li><a href="/' . $navitem[0] . ($lang == 'en' ? '' : '/' . $lang) . '">' . $navitem[1] . '</a></li>' . "\n" );
						}
					?>
				</ul>

				<blockquote style="padding-bottom: 8px; font-size: 14px">
					<h2 style="margin: 0">Links</h2>

                    <a href="https://github.com/Polytonic/Glitter" style="text-decoration: underline">OpenGL boilerplate code</a><br>

                    <a href="https://github.com/zuck/opengl-examples" style="text-decoration: underline">Easy-to-build code</a><br>
                    <a href="https://www.youtube.com/playlist?list=PLW3Zl3wyJwWNQjMz941uyOIq3Nw6bcDYC" style="text-decoration: underline">Matrix math tutorials</a><br>
                    <a href="http://docs.gl" style="text-decoration: underline">OpenGL reference</a>
				</blockquote>

                <div id="adbox">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- Open.GL Responsive -->
                    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-4259747131061893" data-ad-slot="6609097747" data-ad-format="auto"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
			</nav>

			<!-- Content container -->
			<main>
				<article>
					<?php
						include_once("includes/markdown.php");

						print(Markdown($contentSource));
					?>
				</article>

                <div id="adbox-article">
                    <hr />

                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- Open.GL Responsive -->
                    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-4259747131061893" data-ad-slot="6609097747" data-ad-format="auto"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>

				<?php
					if (!$notfound)
					{
				?>

				<!-- Disqus comments -->
				<hr />
				<aside id="disqus_thread"></aside>
				<script type="text/javascript">
					var dsq = document.createElement("script");
					dsq.type = "text/javascript";
					dsq.async = true;
					dsq.src = "//opengl.disqus.com/embed.js";
					document.getElementsByTagName("head")[0].appendChild( dsq );
				</script>
				<?php
					}
				?>
			</main>
		</div>
	</body>
</html>
