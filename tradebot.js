var Steam = require('steam');
var fs = require('fs');
var SteamTrade = require('steam-trade');
var steamTrade = new SteamTrade();


// fs.existsSync(path)#
var sentry = fs.readFileSync("sentry");

var bot = new Steam.SteamClient();
bot.logOn({
  accountName: 'b337138',
  password: 'b337138@rmqkr.net',
  authCode: "KJWGW",
  shaSentryfile: sentry
});
bot.on('loggedOn', function() {
	console.log("logged on");
	bot.setPersonaState(Steam.EPersonaState.Online); // to display your bot's status as "Online"
	bot.setPersonaName('Dotadup'); // to change its nickname

	// test steam trade
	steamTrade.sessionID = bot.webSessionID;
	bot.webLogOn(function(cookies){
		steamTrade.setCookie(cookies);
	});

	steamTrade.open("76561197964515697", function() {
		// start trade with yene
		steamTrade.loadInventory(570, 2, function(items) {
			console.log(items);
		})
	});
});

bot.on('sentry', function(buffer) {
	fs.writeFile('sentry', buffer);
});

bot.on('friend', function(steamID, EFriendRelationship) {
	if (EFriendRelationship === Steam.EFriendRelationship.friend) {
			console.log("send trade to " + steamID);
	} else if (EFriendRelationship === Steam.EFriendRelationship.RequestRecipient) {
		console.log("adding " + steamID);
		bot.addFriend(steamID);
	} else {
		console.log(steamID + " " + EFriendRelationship);
	}
});

var express = require('express');

var app = express();
app.use(express.bodyParser());

app.post('/trade/:id', function(req, res) {
	// http://expressjs.com/api.html#req.body
	console.dir(req.body);
	res.send("ok");
	//res.send({id:req.params.id, data: req.body.data});

	bot.addFriend(req.params.id);
});
 
app.listen(3000);
console.log('Listening on port 3000...');