<?php
require '../pageBuffer.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
    <!-- comments get removed -->
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Dave Shea" />
	<!-- also whitespace around block or undisplayed elements -->
	<meta name="keywords" content="design, css, cascading, style, sheets, xhtml, graphic design, w3c, web standards, visual, display" />
	<meta name="description" content="A demonstration of what can be accomplished visually through CSS-based design." />
	<meta name="robots" content="all" />
	<title>css Zen Garden: The Beauty in CSS Design</title>

	<!-- to correct the unsightly Flash of Unstyled Content. http://www.bluerobot.com/web/css/fouc.asp -->
	<script type="text/javascript"><!--
// js comment inside SCRIPT element
var is = {
    ie:      navigator.appName == 'Microsoft Internet Explorer',
    java:    navigator.javaEnabled(),
    ns:      navigator.appName == 'Netscape',
    ua:      navigator.userAgent.toLowerCase(),
    version: parseFloat(navigator.appVersion.substr(21)) || 
             parseFloat(navigator.appVersion),
    win:     navigator.platform == 'Win32'
}
is.mac = is.ua.indexOf('mac') >= 0;
if (is.ua.indexOf('opera') >= 0) {
    is.ie = is.ns = false;
    is.opera = true;
}
if (is.ua.indexOf('gecko') >= 0) {
    is.ie = is.ns = false;
    is.gecko = true;
}
// --></script>
<script type="text/javascript">
 //<![CDATA[
  var i = 0;
  while  (++i < 10)
  {
    // ...
  }
 //]]>
</script>
<script type="text/javascript">
 /* <![CDATA[ */ i = 1;  /* ]]> */
</script>
<script type="text/javascript">
 (i < 1); /* CDATA needed */
</script>
    <!--[if IE 6]>
    <style type="text/css">
/*! copyright: you'll need CDATA for this < & */
body {background:white;}
    </style>
    <![endif]-->
	<style type="text/css" title="currentStyle" media="screen">
		@import "/001/001.css";
/*\*/ css hack {} /*  */
/* normal CSS comment */
/*/*/ css hack {} /*  */
css hack {
    display/**/:/**/none;
    display:none;
}
	</style>
	<link
		rel="alternate"
		type="application/rss+xml"
		title="RSS"
		href="http://www.csszengarden.com/zengarden.xml" />
</head>
<body id="css-zen-garden">
<!--[if !IE]>--><p>Browser != IE</p><!--<![endif]-->
<div id="container">
		<div id="pageHeader">
			<h1><span>css Zen Garden</span></h1>
			<h2><span>The Beauty of <acronym title="Cascading Style Sheets">CSS</acronym>
Design</span></h2>
		</div>
		<pre>
	White  space  is  important   here!
		</pre>
		<div id="quickSummary">
			<p class="p1"><span>A demonstration of what can be accomplished visually through <acronym title="Cascading Style Sheets">CSS</acronym>-based design. Select any style sheet from the list to load it into this page.</span></p>
			<p class="p2"><span>Download the sample <a href="/zengarden-sample.html" title="This page's source HTML code, not to be modified.">html file</a> and <a href="/zengarden-sample.css" title="This page's sample CSS, the file you may modify.">css file</a></span></p>
		</div>
        <textarea name="comment" id="comment" rows="6" class="maxwidth" cols="80">66666

1234567890</textarea>

</div>
</body>
</html>