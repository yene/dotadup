<?php
session_start();
require "apikey.php";

# Logging in with Google accounts requires setting special identity, so this example shows how to do it.
# The returned Claimed ID will contain the user's 64-bit SteamID. 
# The Claimed ID format is: http://steamcommunity.com/openid/id/<steamid>
require 'openid.php';
try {
    # Change 'localhost' to your domain name.
    $openid = new LightOpenID('localhost');
    if(!$openid->mode) {
        if(isset($_GET['login'])) {
            $openid->identity = 'http://steamcommunity.com/openid';
            header('Location: ' . $openid->authUrl());
        }
    } elseif($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
    	if ($openid->validate()) {
    		$userID = explode("/", $openid->identity)[5];
    		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" .$apikey. "&steamids=" . $userID;
    		$_SESSION['userID'] = $userID;

    		$userDataJson = file_get_contents($url);
    		$userData = json_decode($userDataJson, true);

    		$_SESSION['name'] = $userData["response"]["players"][0]["personaname"];
    		$_SESSION['avatar'] = $userData["response"]["players"][0]["avatarmedium"];;


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
	 <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	 <script>
	 $(document).ready(function(){
		 $( ".itemBox" ).click(function () {
		   $(this).toggleClass("selected");
		 });

		 $( "nav ul li" ).click(function () {
		   $(this).toggleClass("active");
		 });
	 });

		function trade() {
			var count = 0;
			var userID = <?=$_SESSION['userID']?>;
			$( ".itemBox.selected" ).each(function( index ) {
				count++;
				console.log( $(this).attr("data-item-id") );
			});
			
			if (count == 0) {
				alert("Nothing selected.");
			}

		}


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

		.username {
			font-size: 30px;
		}

		.username img {
			vertical-align: middle;
			border-radius: 5px;
		}


	</style>
</head>
<body>
	<header>
		<div class="wrapper">
			<h1 class="title" style="display:inline;">Dotadup</h1>
			<div class="username" style="display:inline; float: right;">
<?php
	if (!isset($_SESSION['userID'])) {
?>
				<a href="?login"><img src="images/sits_small.png"></a>
<?php
	} else {
?>
		<?=$_SESSION['name'] ?>  <img src="<?=$_SESSION['avatar'] ?>">
<?php
	}
?>

			</div>
		</div>
		<nav class="wrapper">
			<ul>
				<li class="active"><a href="#">Trade</a></li>
				<li><a href="#">Trade History</a></li>
				<li><a href="#">Help</a></li>
				<li><a href="#">Donate</a></li>
				<li><a href="#">Settings</a></li>
			</ul>
		</nav>
	</header>
	<section class="wrapper">
		<h1>Your duplicate Items.</h1><p>Please select the wan you want to trade.</p>

<?php

//http://steamcommunity.com/profiles/<PROFILEID>/inventory/json/753/1
//http://steamcommunity.com/id/yene/inventory/json/570/2
// yenes id 76561197964515697

$url = "http://steamcommunity.com/profiles/" .$_SESSION['userID']. "/inventory/json/570/2";

if (isset($_GET["test"])) {
	$url = "http://steamcommunity.com/" . $_GET["test"] . "/inventory/json/570/2";
}

$itemJson = file_get_contents($url);
$items = json_decode($itemJson, true);

/*
	aui_2000
	cyborgmatt
	Robinlee
	Chook
	profiles/76561197980022982
	profiles/76561198073883598
	id/MasterMo66/
*/


$imageUrl = "http://cdn.steamcommunity.com/economy/image/";

// duplicate items in rgInventory have the same classid + instance id

// daten sind in rgDescriptions, key ist 93975462_57949762, classid + instance id

if ($items["success"] === "false") {
	echo $items["Error"];
} else {

	$itemWhitelist = array();
	$itemWhitelist[] = "DOTA_WearableType_Wearable";
	$itemWhitelist[] = "courier";
	$itemWhitelist[] = "DOTA_WearableType_Taunt";

	$itemBlackList = array();
	$itemBlackList[] = "DOTA_OtherType";

	$countedItems = array();
	$douplicateItems = array();

	// count items
	foreach ($items['rgInventory'] as $key => $value) {

		$id = $value["classid"] . "_" . $value["instanceid"];

		// check if item is in the white or black list (tags are checked)
		$skip = TRUE;
		foreach ($items['rgDescriptions'][$id]["tags"] as $key2 => $value2) {
			if (in_array($value2["internal_name"], $itemBlackList)) continue 2;
			if (in_array($value2["internal_name"], $itemWhitelist)) $skip = FALSE;
		}

		if ($skip) continue;

		if (array_key_exists($id, $countedItems)) {
			$countedItems[$id] = $countedItems[$id] + 1;
			$douplicateItems[] = $id;
		} else {
			$countedItems[$id] = 1;
		}
	}

	foreach ($douplicateItems as $key => $value) {
		$image = $imageUrl . $items['rgDescriptions'][$value]['icon_url'];
		?>
		<div class="itemBox" style="background-image: url(<?=$image?>);" 
			title="<?=print_r($items['rgDescriptions'][$value]['tags'], true) ?>"
			data-item-id="<?=$value?>">
			<p class="itemTitle"><?=$items['rgDescriptions'][$value]['name']?></p>
		</div>
		<?php
	}

}

?>
	<br style="clear: both;";>
	<button type="button" onclick="trade()">Send Trade Offer</button>
	</section>
	<footer class="wrapper">
		<p>Dota 2 is a registered trademark of Valve Corporation. This site is not affiliated with Valve Corporation. All game images and names are property of Valve Corporation. <a href="http://steampowered.com/">Powered by Steam</a></p>
	</footer>
</body>
</html>