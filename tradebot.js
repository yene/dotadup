var Steam = require('steam');
var fs = require('fs');
var SteamTrade = require('steam-trade');

var webSessionID = "";
var webCookie = "";

// fs.existsSync(path)#
var sentry = fs.readFileSync("sentry");

// if we've saved a server list, use it
if (fs.existsSync('servers')) {
  Steam.servers = JSON.parse(fs.readFileSync('servers'));
}

/******************************************

Steam login and friend management

******************************************/

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

bot.on('servers', function(servers) {
  fs.writeFile('servers', JSON.stringify(servers));
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

bot.on('error', function(e) {
  console.log("error: " + e.cause);
});

/******************************************

Steam trade

******************************************/

// trade session started
bot.on('sessionStart', function(steamID) {
    var gift = false;
    console.log("sessionStart with steamid " + steamID);
    var steamTrade = new SteamTrade();
    steamTrade.sessionID = webSessionID;
    steamTrade.cookie = webCookie;
    steamTrade.loadInventory(570, 2, function(result) {
      fs.writeFile('my items', result);
    });
    
    steamTrade.open(steamID, function() {
      //add some items immediately
    });

    steamTrade.on('chatMsg', function(message) {
      console.log("he said:" + message);
    });

    steamTrade.on('ready', function(){
      console.log("other is ready for trade");
      steamTrade.ready();
      steamTrade.confirm();
    });

    steamTrade.on('end', function(result, array) {
      console.log("trade has ended");
    });

    // isAdded:  true if an item was added, false if removed
    steamTrade.on('offerChanged', function(isAdded, item) {
    	var symbol = isAdded ? "+" : "-"
      console.log("offer changed: " + symbol + item);

    });
});

bot.on('tradeProposed', function(tradeID, steamID) {
  console.log("starting trade with: "+ steamID);
});

bot.on('tradeResult', function(tradeID, tradeResponse, steamID) {
  console.log("trade finished with: "+ steamID);
});

/******************************************

The webserver which listens for trade offers

******************************************/

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
  bot.trade(req.params.id);
  console.log("requseting trade with: " + req.params.id);

});
 
app.listen(3000);
console.log('Listening on port 3000...');