var cooperhewitt = require('node-cooperhewitt')
var express = require("express");
var logfmt = require("logfmt");
var app = express();

app.use(logfmt.requestLogger());

var api_token = process.env.CH_API_KEY;

app.get('/', function(req, res) {

	var method = 'cooperhewitt.objects.getRandom';
	var args = {'access_token': api_token};

	cooperhewitt.call(method, args, function(rsp){   
	    res.send(rsp);  
	});	
	
});

var port = Number(process.env.PORT || 5000);
app.listen(port, function() {
  console.log("Listening on " + port);
});