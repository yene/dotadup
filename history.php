<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>dotadup - Trade your duplicate Dota 2 items</title>
	<meta name="description" content="Trade your duplicate Dota 2 items">
	<meta name="author" content="yene">
	<meta name="keywords" content="Dota 2, trade, items, dupliacte">
	<!-- Normalize.css is a customisable CSS file that makes browsers render all elements more consistently and in line with modern standards. -->
	<link rel="stylesheet" media="screen" href="https://raw.github.com/necolas/normalize.css/master/normalize.css">
	<link rel="icon" type="image/png" href="icon.png">
	<link rel="shortcut icon" href="favicon.ico">
	<link href="http://cdn.steamcommunity.com/public/css/skin_1/profilev2.css?v=3386317380" rel="stylesheet" type="text/css" >
	<link href="http://cdn.steamcommunity.com/public/shared/css/shared_global.css?v=98446147" rel="stylesheet" type="text/css" >
	<link href="http://cdn.steamcommunity.com/public/css/skin_1/profile_tradeoffers.css?v=102169244" rel="stylesheet" type="text/css" >

	<style>
	/* colors
#F6F4F4 white
#871201 dark red
#EF1A0E bright red
#D8D7E8 grey
#FFF0E9 mild white
	*/

		/* apply a natural box layout model to all elements http://paulirish.com/2012/box-sizing-border-box-ftw/ */
		
		body {
			/* best helvetica */
			font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
			font-weight: 300;
			background-color: #F6F4F4;
			margin: 0;
			padding: 20px;
		}

		.wrapper {
			display: inline-block; 
			width: 100%;
			background-color: #D8D7E8;
			border-radius: 5px;
			padding: 10px;
			margin: 0 0 20px 0;
		}

		header {
		}

		nav {
			display: block;
		}

		nav ul {
			list-style-type: none;
			margin: 0;
			padding: 0;
			margin-bottom: 2px;
			margin-top: 2px;
		}

		nav li {
			display: inline;

			list-style:  none;
			border-radius: 5px;
			padding-left: 12px;
  	  padding-right: 12px;
   		padding-bottom: 8px;
   	  padding-top: 8px;
		}

		nav li:hover {
			background-color: #FFF0E9;
		}

		nav li:hover a {
			color: #871201;
		}

		a {
			color: #871201;
			text-decoration: none;
		}

		.active {
			background-color: #EF1A0E !important;
		}

		.active a {
			color: #F6F4F4 !important;
		}

		.itemBox {
			position: relative;
			border-radius: 10px;
			float: left;
			width: 256px;
			height: 170px;
			border: 1px solid #871201;
			margin: 5px;
			user-select: none;
			-moz-user-select: none;
			-khtml-user-select: none;
			-webkit-user-select: none;
			-o-user-select: none;
			overflow: hidden;
			cursor: default;
		}

		.rarity {
			text-align: center;
			position: absolute;
			bottom: 0px;
			display: table;
			font-size: 20px;
			background-color: #FFF0E9;
			padding-left: 8px;
			padding-right: 8px;
			padding-top: 2px;
			padding-bottom: 2px;
			opacity: 0.8;
			width: 100%;
			font-weight: bold;
			margin: 0;
     }

		.selected {
			border: 3px solid #EF1A0E;
		}

		.title {
			font-size: 60px;
			color: #871201;
		  text-shadow: 0 1px 0 #ccc,
		               0 2px 0 #c9c9c9,
		               0 3px 0 #bbb,
		               0 4px 0 #b9b9b9,
		               0 5px 0 #aaa,
		               0 6px 1px rgba(0,0,0,.1),
		               0 0 5px rgba(0,0,0,.1),
		               0 1px 3px rgba(0,0,0,.3),
		               0 3px 5px rgba(0,0,0,.2),
		               0 5px 10px rgba(0,0,0,.25),
		               0 10px 10px rgba(0,0,0,.2),
		               0 20px 20px rgba(0,0,0,.15);
		}

		.slogan {
			text-shadow: none;
			font-size: 16px;
			margin-left: 20px;
			font-style: italic;
		}

		.username {
			font-size: 30px;
		}

		.username img {
			vertical-align: middle;
			border-radius: 5px;
		}

		button {
			font-size: 12px;
		}

		.tradeoffer_items {
			opacity: 1 !important;
		}

		.tradeoffer_message {
			display: none;
		}

		.tradeoffer_header {
			display: none;
		}

	</style>
</head>
<body>
	<header>
		<div class="wrapper">
			<h1 class="title" style="display:inline;">Dotadup <span class="slogan"></span></h1>
		</div>
		<nav class="wrapper">
			<ul>
				<li><a href="index.php">Request Offer</a></li>
				<li class="active"><a href="history.php">Trade History</a></li>
			</ul>
		</nav>
	</header>
	<section class="wrapper">
	<div class="profile_leftcol profile_subpage_general">
<?php
	$htmlCode = file_get_contents("history.html");
	$htmlCode = str_replace('<head>','<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>',$htmlCode);  
	$oldSetting = libxml_use_internal_errors( true ); // Fehler nicht beachten
	libxml_clear_errors(); 
	$html = new DOMDocument();
	$newdoc = new DOMDocument('1.0');
	$html->loadHTML($htmlCode);
	$xpath = new DOMXPath( $html );
	$tradeoffers = $xpath->query( "//div[@class='tradeoffer']" ); 
	foreach ( $tradeoffers as $tradeoffer ) {
		$result = $xpath->query("div/div[contains(concat(' ',normalize-space(@class),' '),' tradeoffer_items_banner ')]", $tradeoffer);
		$message = $result->item(0)->nodeValue;
		if (strpos($message,'Accepted') === false) continue;

		$result = $xpath->query("div/div[contains(concat(' ',normalize-space(@class),' '),' secondary ')]/div[@class='tradeoffer_items_header']", $tradeoffer);
		$username = $result->item(0)->nodeValue;
		if (strpos($username,'yene') !== false) continue;

		
		// Import the node, and all its children, to the document
		$node = $newdoc->importNode($tradeoffer, true);
		// And then append it to the "<root>" node
		$newdoc->appendChild($node);
		
	}
	echo $newdoc->saveHTML();

	libxml_clear_errors(); 
	libxml_use_internal_errors( $oldSetting ); 

?>


	</div>
	</section>
	<footer class="wrapper">
		<p>Dota 2 is a registered trademark of Valve Corporation. This site is not affiliated with Valve Corporation. All game images and names are property of Valve Corporation. We took all measures to make this site russian proof.</p>
	</footer>
</body>
</html>