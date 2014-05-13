var twilio = require('twilio');
var cooperhewitt = require('node-cooperhewitt');
var express = require("express");
var logfmt = require("logfmt");
var bodyParser = require('body-parser');

var app = express();

app.use(logfmt.requestLogger());
app.use(bodyParser());

var api_token = process.env.CH_API_KEY;

app.get('/', function(req, res) {

	var resp = new twilio.TwimlResponse();
	resp.say('Welcome to object phone! ');
	resp.gather({ timeout:5, numDigits:1, action:'/handler' }, function() {		

		this.say('Press one on your touchtone phone to search the Cooper-Hewitt collection by object ID.');
		this.say('or, For a random object, press 2. ');
		this.say('to hear what Micah has to say, press 3.'); 

	});
	
	resp.say('I\'m sorry, I missed that, please try again. ');

	resp.redirect({url:'./', method:'GET'});

	res.set('Content-Type', 'text/xml');  
	res.send(resp.toString());  
	
});

app.post('/handler', function(req, res) {
	
	var digits = req.body.Digits;

	res.set('Content-Type', 'text/xml');  
	
	var resp = new twilio.TwimlResponse();
	
	if (digits == "1"){
		var resp = new twilio.TwimlResponse();
		resp.gather({ timeout:10, action:'/object' }, function() {		
			this.say('Please enter an object ID followed by the pound key.');
		});
		
		resp.say('I\'m sorry, I missed that, please try again. ');

		resp.redirect({url:'../', method:'GET'});
		res.send(resp.toString());
	}

	resp.say('I\'m sorry, that choice is invalid, please try again. ');
	resp.redirect({url:'../', method:'GET'});
	
	res.send(resp.toString());
	
});

app.post('/object', function(req, res){
	console.log(req.body);
	var digits = req.body.Digits;
	
	res.set('Content-Type', 'text/xml'); 
	
	var method = 'cooperhewitt.objects.getInfo';
	var args = {'access_token': api_token, 'id': digits };

	cooperhewitt.call(method, args, function(rsp){ 
		
		var resp = new twilio.TwimlResponse();
		
		if (rsp['stat'] == 'ok'){	
			resp.say('You\'ve reached ' + rsp.object.title);
			res.send(resp.toString());  
		} else {
			resp.say('Sorry, something went wrong');
			resp.redirect({url:'../', method:'GET'});
			res.send(resp.toString());  
		} 
		
	}); 
	
});


var port = Number(process.env.PORT || 5000);
app.listen(port, function() {
  console.log("Listening on " + port);
});