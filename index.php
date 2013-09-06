<?php
session_start();
$apikey = "86F1ACC15C5F0A97465AA051D68122F6";

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
	<title>dotadup - Trade your duplicate Items</title>
	<meta name="description" content="Trade your duplicate Items">
	<meta name="author" content="yene">
	<meta name="keywords" content="Dota 2, trade, items, dupliacte">
	<!-- Normalize.css is a customisable CSS file that makes browsers render all elements more consistently and in line with modern standards. -->
	<link rel="stylesheet" media="screen" href="https://raw.github.com/necolas/normalize.css/master/normalize.css">

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
			background-color: #D8D7E8;
			border-radius: 10px;
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
		}

		nav li {
			display: inline;
			margin-bottom: 2px;
			margin-top: 2px;
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

		a {
			color: #871201;
		}
		a:hover {
			color: ;
		}

	</style>
</head>
<body>
	<header>
		<div class="wrapper">
			<h1>Dotadup</h1>
			<div>
<?php
	if (!isset($_SESSION['userID'])) {
?>
				<a href="?login"><img src="images/sits_small.png"></a>
<?php
	} else {
?>
		<p><?=$_SESSION['name'] ?>  <img src="<?=$_SESSION['avatar'] ?>"></p>
<?php
	}
?>

			</div>
		</div>
		<nav class="wrapper">
			<ul>
				<li class="active"><a href="">Trade</a></li>
				<li><a href="">About</a></li>
				<li><a href="">Help</a></li>
				<li><a href="">Donate</a></li>
				<li><a href="">Settings</a></li>
			</ul>
		</nav>
	</header>
	<br style="clear: both;">
	<section class="wrapper">
		ok did it work?
		<div>result</div>
	</section>
	<footer class="wrapper">
		<p>Dota 2 is a registered trademark of Valve Corporation. This site is not affiliated with Valve Corporation. All game images and names are property of Valve Corporation. <a href="http://steampowered.com/">Powered by Steam</a></p>
	</footer>
</body>
</html>