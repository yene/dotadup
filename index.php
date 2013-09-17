<?php
session_start();
require "apikey.php";
$host = ($_SERVER['SERVER_ADDR'] === "::1" || $_SERVER['SERVER_ADDR'] === "127.0.0.1" ) ? "localhost" : "dotadup.com";

# Logging in with Google accounts requires setting special identity, so this example shows how to do it.
# The returned Claimed ID will contain the user's 64-bit SteamID. 
# The Claimed ID format is: http://steamcommunity.com/openid/id/<steamid>
require 'openid.php';
try {
    # Change 'localhost' to your domain name.
    $openid = new LightOpenID($host);
    if(!$openid->mode) {
        if(isset($_GET['login'])) {
            $openid->identity = 'http://steamcommunity.com/openid';
            header('Location: ' . $openid->authUrl());
            exit();
        }
    } elseif($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
    	if ($openid->validate()) {
    		$userID = explode("/", $openid->identity);
    		$userID = $userID[5];
    		$_SESSION['userID'] = $userID;
    		header('Location: http://' . $host);
    		exit();
    	} else {
    		// not logged in
    	}
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}

?>
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
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script>
	$(document).ready(function(){
		$(".itemBox" ).mousedown(function(){ return false; })
		$( ".itemBox" ).click(function () {
			$(this).toggleClass("selected");
		});

		$( "nav ul li" ).click(function () {
			$(this).toggleClass("active");
		});
	});

<?php
	if (isset($_SESSION['userID'])) {
?>
		function trade() {
			var count = 0;
			var userID = <?=$_SESSION['userID']?>;
			var data = new Array();
			$( ".itemBox.selected" ).each(function( index ) {
				count++;
				var itemID = $(this).attr("data-item-id");
				data.push(itemID)
			});
			
			if (count == 0) {
				alert("Nothing selected.");
			} else {
				var url = "http://<?=$host?>:3000/trade/<?=$_SESSION['userID']?>";
				var data = "items=" + data.join(",");
				$.post(url, data);
				alert("Trade offer is on the way.\nPlease accept the friend request and then the offer.");
			}
		}

		function donate() {
			var count = 0;
			var userID = <?=$_SESSION['userID']?>;
			var data = new Array();
			$( ".itemBox.selected" ).each(function( index ) {
				count++;
				var itemID = $(this).attr("data-item-id");
				data.push(itemID)
			});
			
			if (count == 0) {
				alert("Nothing selected.");
			} else {
				var url = "http://<?=$host?>:3000/donate/<?=$_SESSION['userID']?>";
				var data = "items=" + data.join(",");
				$.post(url, data);
				alert("Trade offer is on the way.\nPlease accept the friend request and then the offer.");
			}
		}
<?php
}
?>
	 </script>


	<style>
	/* colors
#F6F4F4 white
#871201 dark red
#EF1A0E bright red
#D8D7E8 grey
#FFF0E9 mild white
	*/

		/* apply a natural box layout model to all elements http://paulirish.com/2012/box-sizing-border-box-ftw/ */
		* { -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; }
		
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
			background-color: #EF1A0E;
		}

		.active a {
			color: #F6F4F4;
		}

		.itemBox {
			float: left;
			width: 256px;
			height: 170px;
			border: 1px solid #871201;
			padding: 10px;
			margin: 5px;
			user-select: none;
			-moz-user-select: none;
			-khtml-user-select: none;
			-webkit-user-select: none;
			-o-user-select: none;
			cursor: default;
		}

		.itemTitle {
			text-align: center;
			top: 90px;
			position: relative;
			display: table;
    	margin: 0 auto;
			font-size: 20px;
			background-color: #FFF0E9;
			border-radius: 10px;
			padding-left: 8px;
			padding-right: 8px;
			padding-top: 2px;
			padding-bottom: 2px;
			opacity: 0.8;
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


	</style>
</head>
<body>
	<header>
		<div class="wrapper">
			<h1 class="title" style="display:inline;">Dotadup <span class="slogan">away with the dupes</span</h1>
		</div>
		<!--
		<nav class="wrapper">
			<ul>
				<li class="active"><a href="#">Trade</a></li>
				
				<li><a href="#">Trade History</a></li>
				<li><a href="#">Help</a></li>
				<li><a href="#">Donate</a></li>
				<li><a href="#">Settings</a></li>
			
			</ul>
		</nav>	-->
	</header>
	<section class="wrapper">
<?php
	if (!isset($_SESSION['userID'])) {
?>
	<h1>Select your duplicate Items and we send you a Steam Trade Offer.</h1>
	<p>But first please sign in through Steam.</p>
	<p><a href="?login"><img src="images/sits_small.png"></a></p>
<?php
	} else {
?>
		<h1>Select your duplicate Items and we send you a Steam Trade Offer.</h1>
		<p>For every item selected we try to offer you another one with the same rarity.</p>

<?php

	$url = "http://steamcommunity.com/profiles/" . $_SESSION['userID'] . "/inventory/json/570/2?trading=1";

	if (isset($_GET["test"])) {
		/*
		  profile/76561197964515697
			id/aui_2000
			id/cyborgmatt
			id/Robinlee
			id/Chook
			profiles/76561197980022982
			profiles/76561198073883598
			id/MasterMo66/
		*/
		$url = "http://steamcommunity.com/" . $_GET["test"] . "/inventory/json/570/2?trading=1";
	}

	$itemJson = file_get_contents($url);
	$items = json_decode($itemJson, true);

	$imageUrl = "http://cdn.steamcommunity.com/economy/image/";

	if ($items["success"] === "false") {
		echo $items["Error"];
		// TODO show message that he needs to make inventory pubilc (i think)
	} else {

		$mergedItems = $items['rgInventory'];

		foreach ($mergedItems as $key => &$value) {
			$itemID = $value["classid"] . "_" . $value["instanceid"];
			$value = array_merge($value, $items['rgDescriptions'][$itemID]);
		}

		$douplicateItems = array();

		foreach ($mergedItems as $key => $value) {

			// only show items that heroes can wear, are not from another type, and are Rare, Uncommon or common
			$isWearable = false;
			$rarity = "";
			$rarityColor = "";
			foreach ($value["tags"] as $key2 => $value2) {
				if ($value2["internal_name"] === "DOTA_OtherType") continue 2;
				if ($value2["internal_name"] === "DOTA_WearableType_Wearable") $isWearable = true;
				if ($value2["category"] === "Rarity") {
					$rarity = $value2["name"];
					$rarityColor = $value2["color"];

					if (! ($rarity === "Rare" || $rarity === "Uncommon" || $rarity === "Common")) {
						continue 2;
					}
				}
			}

			if (!$isWearable) continue;

			if (!in_array($value["classid"] . "_" . $value["instanceid"], $douplicateItems)) {
				$douplicateItems[] = $value["classid"] . "_" . $value["instanceid"];
			} else {
				// duplicate found
				$image = $imageUrl . $value['icon_url'];
				?>
				<div class="itemBox" style="background-image: url(<?=$image?>);" 
					data-item-id="<?=$key?>">
					<p class="itemTitle"><?=$value['name']?></p>
					<p class="rarity" style="color: #<?=$rarityColor?>;"><?=$rarity?></p>
				</div>
				<?php
			}
		}
	}
?>
	<br style="clear: both;";>
	<button type="button" onclick="trade()">Send me a Steam Trade Offer</button>
	or 
	<button type="button" onclick="donate()">I want to donate the items</button>

<?php
}
?>

	</section>
	<footer class="wrapper">
		<p>Dota 2 is a registered trademark of Valve Corporation. This site is not affiliated with Valve Corporation. All game images and names are property of Valve Corporation. We took all measures to make this site russian proof.</p>
	</footer>
</body>
</html>