var twilio = require('twilio');
var cooperhewitt = require('node-cooperhewitt');
var express = require("express");
var logfmt = require("logfmt");
var bodyParser = require('body-parser');

var app = express();

app.use(logfmt.requestLogger());
app.use(bodyParser());

var api_token = process.env.CH_API_KEY;


// ################ VOICE STUFF ###########################

app.get('/', function(req, res) {

	var resp = new twilio.TwimlResponse();
	resp.say('Welcome to object phone! ');
	resp.gather({ timeout:5, numDigits:1, action:'/handler' }, function() {		

		this.say('Press one on your touchtone phone to search the Cooper-Hewitt collection by object ID.');
		this.say('or, For a random object, press 2. ');
		this.say('to hear what Micah has to say, press 3.'); 

	});
	
	resp.say('I\'m sorry, I missed that, please try again. ');

	resp.redirect('/', {method:'GET'});

	res.set('Content-Type', 'text/xml');  
	res.send(resp.toString());  
	
});

app.post('/handler', function(req, res) {
	
	var digits = req.body.Digits;

	res.set('Content-Type', 'text/xml');  
	
	var resp = new twilio.TwimlResponse();
	
	if (digits == "1"){
		resp.gather({ timeout:10, action:'/object' }, function() {		
			this.say('Please enter an object ID followed by the pound key.');
		});
		
		resp.say('I\'m sorry, I missed that, please try again. ');

		resp.redirect('/', {method:'GET'});
		res.send(resp.toString());
	}
	
	if (digits == "2"){
		resp.redirect('/random', {method:'POST'});
		res.send(resp.toString());
	}
	
	if (digits == "3"){
		resp.redirect('/wwms', {method:'POST'});
		res.send(resp.toString());
	}

	resp.say('I\'m sorry, that choice is invalid, please try again. ');
	resp.redirect('/', {method:'GET'});
	
	res.send(resp.toString());
	
});

app.post('/object', function(req, res){

	var digits = req.body.Digits;
	
	res.set('Content-Type', 'text/xml'); 
	
	var method = 'cooperhewitt.objects.getInfo';
	var args = {'access_token': api_token, 'id': digits };

	cooperhewitt.call(method, args, function(rsp){ 
			
		if (rsp['stat'] == 'ok'){	
			var resp = process_voice_object(rsp.object);
			res.send(resp.toString()); 
		} else {
			var resp = new twilio.TwimlResponse();
			resp.say('Sorry, something went wrong');
			resp.redirect('/', {method:'GET'});
			res.send(resp.toString()); 		
		}
		
	}); 
	
});

app.post('/random', function(req, res){
	
	res.set('Content-Type', 'text/xml'); 

	var method = 'cooperhewitt.objects.getRandom';
	var args = {'access_token': api_token};
	
	cooperhewitt.call(method, args, function(rsp){

		if (rsp['stat'] == 'ok'){	
			var resp = process_voice_object(rsp.object);
			res.send(resp.toString()); 
		} else {
			var resp = new twilio.TwimlResponse();
			resp.say('Sorry, something went wrong');
			resp.redirect('/', {method:'GET'});
			res.send(resp.toString()); 		
		}
		
	});
	
});

app.post('/wwms', function(req, res){

	res.set('Content-Type', 'text/xml'); 

	var method = 'cooperhewitt.labs.whatWouldMicahSay';
	var args = {'access_token': api_token};
	
	cooperhewitt.call(method, args, function(rsp){
	
		micahSays = rsp.micah.says;
	
		var resp = new twilio.TwimlResponse();
		resp.say(micahSays);
		resp.redirect('/', {method:'GET'});
		res.send(resp.toString());
		
	});

	
});

function process_voice_object(rsp){
	
	var resp = new twilio.TwimlResponse();
	
	object_id = rsp.id;
	medium = rsp.medium;
	title = rsp.title;
	
	phrase = "Hi, you've reached ";
	
	if (title){
		phrase = phrase + title + ". "
	}
	
	if (medium){
		phrase = phrase + "My medium is " + medium + ". "
	}
	
	resp.say(phrase.toString());
	
	if (object_id == '18639541'){
		resp.say("We also have an Object of the day post for this object, which you cna listen to now.");
		resp.play("http://audio.spokenlayer.com/cooperhewitt-org/2014/02/ab03ff8bdd197d7b5dd5d3525a42eb50/ab03ff8bdd197d7b5dd5d3525a42eb50-geoff-mayo.mp3");
	}
	
	resp.redirect('/', {method:'GET'});
	return resp;
}

// ################ SMS STUFF ###########################

app.get('/sms', function(req, res){
	
	var body = req.query.Body;

    if (body.toLowerCase() == 'wtf')
        resp = sms_help();
    else if (body.toLowerCase() == 'wwms')
        console.log(body.toLowerCase()); //rsp = wwms()
    else if (body.toLowerCase == 'random')
        console.log(body.toLowerCase()); //rsp = random()    
    //else if (is_it_an_int(body))
        //rsp = get_by_object_id(body)
    //else    
        //rsp = get_by_accession_number(body)
			
	res.send(resp.toString());
	
});

function sms_help(){
	
	var resp = new twilio.TwimlResponse();
	
    phrase = "Thanks for texting me. It looks like you could use some help. "
    
    phrase = phrase + "Try texting the word 'random' to read about a random object from the collection. "
    
    phrase = phrase + "You can also text me an object ID number, or an accession number. "
    
    phrase = phrase + "To see what Micah might say about this, text 'wwms' and to re-read the help just text 'wtf' at any time. "
    
	resp.sms(phrase.toString());
    
	return resp;	
}

var port = Number(process.env.PORT || 5000);
app.listen(port, function() {
  console.log("Listening on " + port);
});