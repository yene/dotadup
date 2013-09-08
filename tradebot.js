var Steam = require('steam');
var fs = require('fs');
var SteamTrade = require('steam-trade');
var steamTrade = new SteamTrade();

var webSessionID = "";
var webCookie = "";

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
});

bot.on('sentry', function(buffer) {
	fs.writeFile('sentry', buffer);
});

bot.on('webSessionID', function(sessionID) {
  webSessionID = sessionID;
  console.log("webSessionID: " + webSessionID); 
  // If you are using Steam Community (including trading), 
  // you should call webLogOn again, since your current cookie is no longer valid.
  bot.webLogOn(function(cookie) {
    console.log("webCookie: " + cookie);
    webCookie = cookie;
  });
});



bot.on('message', function(source, message, type, chatter) {
  if (message === "") {return;}
  // respond to both chat room and private messages
  console.log('Received message: ' + message);
  if (message == 'ping') {
    bot.sendMessage(source, 'pong', Steam.EChatEntryType.ChatMsg); // ChatMsg by default
  }
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

	// start trade
  steamTrade.open(req.params.id, function() {
    //add some items immediately
    console.log("start trade with " + req.params.id);

  });

});
 
app.listen(3000);
console.log('Listening on port 3000...');