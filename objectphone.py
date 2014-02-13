import sys
import os
from flask import Flask, request, redirect
import twilio.twiml

import cooperhewitt.api.client

token = os.environ['CH_API_KEY']

app = Flask(__name__)

@app.route('/', methods=['GET','POST'])
def hello():
	r = twilio.twiml.Response()
	r.say("Welcome to object phone!. ") 
	r.say("I'm sorry, but we are currently performing some maintenance. Please feel free to talk to us by SMS for the time being! ")
	return str(r)


@app.route('/sms', methods=['GET','POST'])
def sms():
    
    body = request.values.get('Body', None)

    if (body):
        sms_text = process_body(body)
    else:
        sms_text = sms_help()
        
    r = twilio.twiml.Response()
    r.message(sms_text)
    return str(r)

def wwms():
    api = cooperhewitt.api.client.OAuth2(token)
    rsp = api.call('cooperhewitt.labs.whatWouldMicahSay')

    micah = rsp['micah']
    says = micah['says']

    return says
    
def random():
    api = cooperhewitt.api.client.OAuth2(token)
    rsp = api.call('cooperhewitt.objects.getRandom')

    random = rsp['object']
    
    return process_sms_object(random)

def get_by_object_id(object_id):

    api = cooperhewitt.api.client.OAuth2(token)
    method = 'cooperhewitt.objects.getInfo'
    args = { 'id': object_id }
		
    rsp = api.call(method, **args)

    if (rsp['stat'] == 'ok'):
        obj = rsp['object']
        return process_sms_object(obj)
    else:
        return sms_oops()

def get_by_accession_number(accession):

    api = cooperhewitt.api.client.OAuth2(token)
    method = 'cooperhewitt.objects.getInfo'

    enc_accession = accession.encode("utf-8")
    print enc_accession
    
    args = { 'accession_number': enc_accession }
		
    rsp = api.call(method, **args)

    if (rsp['stat'] == 'ok'):
        obj = rsp['object']
        return process_sms_object(obj)
    else:
        return sms_oops()
    
def sms_help():
    
    phrase = "Thanks for texting me. It looks like you could use some help. "
    
    phrase = phrase + "Try texting the word 'random' to read about a random object from the collection. "
    
    phrase = phrase + "You can also text me an object ID number, or an accession number. "
    
    phrase = phrase + "To see what Micah might say about this, text 'wwms' and to re-read the help just text 'wtf' at any time. "
    
    return phrase
    
def sms_oops():
    return "Oops, looks like something ain't right. Please text 'wtf' if you are having trouble. "
    
        
def process_sms_object(obj):
    
    object_id = obj['id']
    medium = obj['medium']
    title = obj['title']
    
    phrase = "Thanks for texting me. "
    
    if (title):
		phrase = phrase + "I'm called " + title + ". "
    
    if (medium):
        phrase = phrase + "My medium is " + medium + ". "
    
    shorten = encode(int(obj['id']))    
    phrase = phrase + "To read more about me, click http://cprhw.tt/o/" + shorten
        
    return phrase
    
def process_body(body):
    
    # could be a bunch of stuff .. first check for words
    if body.lower() == 'wtf':
        rsp = sms_help()
    elif body.lower() == 'wwms':
        rsp = wwms()
    elif body.lower() == 'random':
        rsp = random()    
    elif is_it_an_int(body):
        rsp = get_by_object_id(body)
    else:    
        rsp = get_by_accession_number(body)
        
    return rsp    

def is_it_an_int(s):
    try: 
        int(s)
        return True
    except ValueError:
        return False

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

if __name__ == '__main__':

    if not os.environ.get('CH_API_KEY', False):
        print "You forgot to set your CH API key as an environment variable"
        sys.exit()
            
    #rsp = sms()
    #print rsp