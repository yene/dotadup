var Steam = require('steam');
var fs = require('fs');
var SteamTrade = require('steam-trade');
var SteamOffer = require('steam-offer');

var webSessionID = "";
var webCookie = "";

// fs.existsSync(path)#
var sentry = fs.readFileSync("sentry");

// if we've saved a server list, use it
if (fs.existsSync('servers')) {
  Steam.servers = JSON.parse(fs.readFileSync('servers'));
}

var offers = {};


/******************************************

Steam trade offer

******************************************/
function makeOffer(steamID, items) {
  console.log("make offer to: " + steamID);
  var steamOffer = new SteamOffer();
  steamOffer.sessionID = webSessionID;
  for (var key in webCookie) {
    steamOffer.setCookie(webCookie[key]);
  }
  steamOffer.miniprofile(steamID, function(miniprofile) {
    steamOffer.open(steamID, miniprofile, function() {
      steamOffer.loadInventory('570', '2', function(myInventory) {
        shuffle(myInventory);

        steamOffer.loadForeignInventory('570', '2', function(partnerInventory) {
          for (var inventoryKey in myInventory) {
            var myItem = myInventory[inventoryKey];
            if (typeof myItem === "undefined") {
              continue;
            }

            // remove items he already has from my inventory
            for (var key in partnerInventory) {
              var partnerItem = partnerInventory[key];

              if (partnerItem.classid === myItem.classid && partnerItem.instanceid === myItem.instanceid) {
                delete myInventory[inventoryKey];
                continue;
              }
            }

            // filter my items
            // only show items that heroes can wear, are not from another type, and are Rare, Uncommon or common
            var isWearable = false;
            for (var tagKey in myItem.tags) {
              if (myItem.tags[tagKey].category === "Rarity") {
                rarity = myItem.tags[tagKey].name;
                if (!(rarity === "Rare" || rarity === "Uncommon" || rarity === "Common")) {
                  delete myInventory[inventoryKey];
                  break;
                }
              }
              if (myItem.tags[tagKey].internal_name === "DOTA_OtherType") {
                delete myInventory[inventoryKey];
                break;
              }
              if (myItem.tags[tagKey].internal_name === "DOTA_WearableType_Wearable") isWearable = true;
            }

            if (!isWearable) {
              delete myInventory[inventoryKey];
            }
          }

          var me_assets = new Array();
          var them_assets = new Array();

          for (var offeredItemKey in items) {
            var item = items[offeredItemKey];
            var offeredItem = partnerInventory[item];
            var offeredItemRarity = steamOffer.getRarity(offeredItem);

            // find a match by rarity
            for (var myItemKey in myInventory) {
              var myItem = myInventory[myItemKey];
              if (typeof myItem === "undefined") { // this item is already taken
                continue;
              }

              var myItemRarity = steamOffer.getRarity(myItem);

              if (myItemRarity === offeredItemRarity) {
                console.log("found " + myItem.name + " for " + offeredItem.name);
                me_assets.push({"appid":570,"contextid":2,"amount":1,"assetid":myItem.id});
                them_assets.push({"appid":570,"contextid":2,"amount":1,"assetid":offeredItem.id});
                // remove item from the item pool
                delete myInventory[myItemKey];
                break;
              }
            }
          }

          steamOffer.sendOffer(me_assets, them_assets, 'Thank you for using dotadup.com', function(partnerInventory) {
            console.log("offer sent to: " + steamID);
            bot.removeFriend(steamID);
          });
        });
      });
     })
  });
 
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
	bot.setPersonaName('Dotadup'); // to change its nickname
});

bot.on('sentry', function(buffer) {
	fs.writeFile('sentry', buffer);
});

bot.on('webSessionID', function(sessionID) {
  webSessionID = sessionID;
  // If you are using Steam Community (including trading), 
  // you should call webLogOn again, since your current cookie is no longer valid.
  bot.webLogOn(function(cookie) {
    webCookie = cookie;
    // go online after you got the community login
    bot.setPersonaState(Steam.EPersonaState.Online); // to display your bot's status as "Online"
    console.log("online");
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
  //  makeOffer(userID, items);

	if (EFriendRelationship === Steam.EFriendRelationship.Friend) {
			console.log("send trade to " + steamID);
      if (offers.hasOwnProperty(steamID)) {
        console.log("there is a trade waiting for you");
        makeOffer(steamID, offers[steamID]);
        delete offers[steamID];
      }
	} else if (EFriendRelationship === Steam.EFriendRelationship.RequestRecipient) {
		console.log("adding " + steamID);
		bot.addFriend(steamID);
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
    for (var key in webCookie) {
    	steamTrade.setCookie(webCookie[key]);
    }

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
    	var action = isAdded ? "added " : "removed "
      console.log("offer changed: " + action + item.name);
    });
});

bot.on('tradeProposed', function(tradeID, steamID) {
  console.log("starting trade with: "+ steamID);
  bot.respondToTrade(tradeID, true);
});

bot.on('tradeResult', function(tradeID, tradeResponse, steamID) { // EEconTradeResponse.Accepted = 1
  console.log("trade with: "+ steamID + " said " + tradeResponse);
});

bot.on('tradeOffers', function(tradeCount) {
  console.log("trade count is: "+ tradeCount);
});

/******************************************

The webserver which listens for trade offers

******************************************/

var express = require('express');

var app = express();
app.use(express.bodyParser());

app.post('/trade/:id', function(req, res) {
	// http://expressjs.com/api.html#req.body
  res.send(200);

  var items = req.body.items.split(',');
  var userID = req.params.id;

  bot.addFriend(userID);
  offers[userID] = items;
});
 
app.get('/', function(req, res){
  res.send('hello world');
});

app.listen(3000);
console.log('Listening on port 3000...');


/******************************************

Helper functions

******************************************/

function shuffle(array) {
  var currentIndex = array.length
    , temporaryValue
    , randomIndex
    ;

  // While there remain elements to shuffle...
  while (0 !== currentIndex) {

    // Pick a remaining element...
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex -= 1;

    // And swap it with the current element.
    temporaryValue = array[currentIndex];
    array[currentIndex] = array[randomIndex];
    array[randomIndex] = temporaryValue;
  }

  return array;
}

function readableObject(anObject) {
  return JSON.stringify(anObject, null, 4);
}