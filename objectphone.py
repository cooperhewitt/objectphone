import os
from flask import Flask, request, redirect
from twilio import twiml
import cStringIO
import pycurl
import urllib
import simplejson as json

api_token = os.environ['CH_API_KEY']

app = Flask(__name__)

@app.route('/')
def hello():
	r = twiml.Response()
	r.say("Welcome to object phone!. ") 
	with r.gather(timeout=10, numDigits=1, action="initial-handler", method="POST") as g:
		g.say("Press one on your touchtone phone to search the Cooper-Hewitt collection by object ID. ")
		g.say("or, For a random object, press 2. ")
		
	r.say("I'm sorry, I missed that, please try again. ")
	r.redirect(url="/", method="GET")	
	return str(r)
	
@app.route('/initial-handler', methods=['GET','POST'])
def handlecall():
	digits = request.values.get('Digits', None)
	r = twiml.Response()
	if (digits == "1"):
		with r.gather(action="object", method="POST") as g:
			g.say("Please enter an object ID followed by the pound key.")
		return str(r)
	
	if (digits == "2"):
		return redirect("/random")
	
	return "ok"
		

@app.route('/object', methods=['GET','POST'])
def obj():
	obj_id = request.values.get('Digits', None)
	r = twiml.Response()
	
	buf = cStringIO.StringIO()
	c = pycurl.Curl()
	c.setopt(c.URL, 'https://api.collection.cooperhewitt.org/rest')
	d = {'method':'cooperhewitt.objects.getInfo','access_token':api_token, 'id':obj_id}
	c.setopt(c.WRITEFUNCTION, buf.write)
	c.setopt(c.POSTFIELDS, urllib.urlencode(d) )
	c.perform()
	
	rsp_obj = json.loads(buf.getvalue())
	buf.reset()
	buf.truncate()
	object_id = rsp_obj.get('object', [])
	medium = object_id.get('medium', [])
	title = object_id.get('title', [])
	
	phrase = "Hi you've reached  "

	if (title):
		phrase = phrase + title + ". "
		
	if (medium):
		phrase = phrase + "My medium is " + medium + ". "
			
	r.say(phrase)
	return str(r)


@app.route('/random')
def random():
	buf = cStringIO.StringIO()
	c = pycurl.Curl()
	c.setopt(c.URL, 'https://api.collection.cooperhewitt.org/rest')
	d = {'method':'cooperhewitt.objects.getRandom','access_token':api_token}
	c.setopt(c.WRITEFUNCTION, buf.write)
	c.setopt(c.POSTFIELDS, urllib.urlencode(d) )
	c.perform()
	
	random = json.loads(buf.getvalue())
	buf.reset()
	buf.truncate()
	object_id = random.get('object', [])
	medium = object_id.get('medium', [])
	title = object_id.get('title', [])
	
	phrase = "Thanks, we are looking up a random object just for you. "
	if (title):
		phrase = phrase + "Hi, you've reached " + title + ". "
		
	if (medium):
		phrase =  phrase + "My medium is " + medium + ". "
			
	
	r = twiml.Response()
	r.say(phrase)
	return str(r)
	
@app.route('/sms', methods=['GET','POST'])
def object():
	obj_id = request.values.get('Body', None)
	r = twiml.Response()
	
	buf = cStringIO.StringIO()
	c = pycurl.Curl()
	c.setopt(c.URL, 'https://api.collection.cooperhewitt.org/rest')
	d = {'method':'cooperhewitt.objects.getInfo','access_token':api_token, 'id':obj_id}
	c.setopt(c.WRITEFUNCTION, buf.write)
	c.setopt(c.POSTFIELDS, urllib.urlencode(d) )
	c.perform()
	
	rsp_obj = json.loads(buf.getvalue())
	buf.reset()
	buf.truncate()
	object_id = rsp_obj.get('object', [])
	medium = object_id.get('medium', [])
	title = object_id.get('title', [])
	
	phrase = "Thanks for texting me. "

	if (title):
		phrase = phrase + "I'm called " + title + ". "
		
	if (medium):
		medium_phrase = "My medium is " + medium + ". "
	
	url = encode(int(obj_id))
	url = "To read more about me, click http://cprhw.tt/o/" + str(url)
	
	r.sms(phrase)
	r.sms(medium_phrase)
	r.sms(url)
	
	return str(r)
	
def encode(num):
	alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'
	base_count = len(alphabet)
	encode = ''
	
	if (num < 0):
		return ''
	
	while (num >= base_count):	
		mod = num % base_count
		encode = alphabet[mod] + encode
		num = num / base_count

	if (num):
		encode = alphabet[num] + encode

	return encode
	
	
	
