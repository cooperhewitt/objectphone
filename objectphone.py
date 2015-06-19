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
    with r.gather(timeout=5, numDigits=1, action="initial-handler", method="POST") as g:
        g.play("https://s3.amazonaws.com/objectphone.cooperhewitt.org/menu/MAIN_MENU_1.mp3") 
        g.play("https://s3.amazonaws.com/objectphone.cooperhewitt.org/menu/MAIN_MENU_2.mp3") # press 1 to listen to adjay
    	g.play("https://s3.amazonaws.com/objectphone.cooperhewitt.org/menu/MAIN_MENU_3.mp3") # search by obj ID, press 2
        g.play("https://s3.amazonaws.com/objectphone.cooperhewitt.org/menu/MAIN_MENU_4.mp3") # random object, press 3
        g.play("https://s3.amazonaws.com/objectphone.cooperhewitt.org/menu/MAIN_MENU_5.mp3") # return to this menu, press 0

    r.say("I'm sorry, I missed that, please try again. ")
    r.redirect(url="/", method="GET")	
    return str(r)
    
@app.route('/initial-handler', methods=['GET','POST'])
def handlecall():
    digits = request.values.get('Digits', None)

    r = twilio.twiml.Response()

    if (digits == "1"):
    	r.redirect(url="/adjay", method="GET")
    	return str(r)

    if (digits == "2"):
    	with r.gather(timeout=10, action="object", method="POST") as g:
    		g.play("https://s3.amazonaws.com/objectphone.cooperhewitt.org/menu/OBJECT_MENU_1.mp3")

    	r.say("I'm sorry, I missed that, please try again. ")
    	r.redirect(url="/initial-handler?Digits=1", method="POST", )
    	return str(r)

    if (digits == "3"):
    	r.redirect(url="/random", method="GET")
    	return str(r)

    if (digits == "0"):
        r.redirect(url="/", method="GET")
        return str(r)

    r.say("I'm sorry, that choice is invalid, please try again. ")
    r.redirect(url="/", method="GET")	
    return str(r)

@app.route('/object', methods=['GET','POST'])
def object():
    object_id = request.values.get('Digits', None)
    r = twilio.twiml.Response()

    api = cooperhewitt.api.client.OAuth2(token)
    method = 'cooperhewitt.objects.getInfo'
    args = { 'id': object_id }

    rsp = api.call(method, **args)

    if (rsp['stat'] == 'ok'):
        obj = rsp['object']
        return process_voice_object(obj)
    else:
        return voice_oops()

@app.route('/adjay', methods=['GET','POST'])
def adjay():
    r = twilio.twiml.Response()
    
    r.play("https://s3.amazonaws.com/objectphone.cooperhewitt.org/menu/ADJAY_MENU_1.mp3")
    r.play("https://s3.amazonaws.com/objectphone.cooperhewitt.org/menu/ADJAY_MENU_2.mp3")
    
    with r.gather(timeout=10, action="object", method="POST") as g:
    	g.play("https://s3.amazonaws.com/objectphone.cooperhewitt.org/menu/ADJAY_MENU_3.mp3")

    with r.gather(timeout=5, numDigits=1, action="adjay-all", method="POST") as g:
        g.play("https://s3.amazonaws.com/objectphone.cooperhewitt.org/menu/ADJAY_MENU_4.mp3")

    r.play("I'm sorry, I missed that, please try again. ")
    r.redirect(url="/adjay", method="POST", )
    return str(r)

@app.route('/random')
def random():

    api = cooperhewitt.api.client.OAuth2(token)
    rsp = api.call('cooperhewitt.objects.getRandom')

    random = rsp['object']

    return process_voice_object(random)
    
@app.route('/wwms')
def wwms_voice():
    r = twilio.twiml.Response()

    api = cooperhewitt.api.client.OAuth2(token)
    rsp = api.call('cooperhewitt.labs.whatWouldMicahSay')

    micah = rsp['micah']
    says = micah['says']

    r.say(says)
    r.redirect(url="/end-menu", method="GET")
    return str(r)
    
        
def process_voice_object(obj):
    r = twilio.twiml.Response()
    
    object_id = obj['id']
 
 
    ### featured exhibit for David Adjay - Eventually all this should be pulled from the API.
    
    # Adjay exhibit 1
    if object_id == '18411459':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18411459.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 2   
    if object_id == '18411461':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18411461.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 3  
    if object_id == '18433051':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18433051.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 4
    if object_id == '18440995':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18440995.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 5   
    if object_id == '18441029':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18441029.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 6   
    if object_id == '18467843':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18467843.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 7
    if object_id == '18467869':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18467869.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 8
    if object_id == '18467877':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18467877.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 9
    if object_id == '18497699':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18497699.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)
        
    # Adjay exhibit 10
    if object_id == '18628819':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18628819.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 11
    if object_id == '18643743':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18643743.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 12
    if object_id == '18687429':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18687429.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 13
    if object_id == '18698497':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18698497.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    # Adjay exhibit 14
    if object_id == '18705685':
        r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18705685.mp3')
        r.redirect(url="/end-menu", method="GET")
        return str(r)

    medium = obj['medium']
    title = obj['title']

    phrase = "Hi you've reached  "

    if (title):
    	phrase = phrase + title + ". "

    if (medium):
    	phrase = phrase + "My medium is " + medium + ". "
	
    r.say(phrase)
    
    if object_id == '18639541':
        r.say("We also have an Object of the day post for this object, which you cna listen to now.")
        r.play('http://audio.spokenlayer.com/cooperhewitt-org/2014/02/ab03ff8bdd197d7b5dd5d3525a42eb50/ab03ff8bdd197d7b5dd5d3525a42eb50-geoff-mayo.mp3')
        
    r.redirect(url="/end-menu", method="GET")
    return str(r)
 
def voice_oops():
    r = twilio.twiml.Response()

    phrase = "Oops, looks like something ain't right. Please hold the line. "
    r.say(phrase)
    r.redirect(url="/end-menu", method="GET")
    return str(r)
    
@app.route('/adjay-all')
def adjay_all():
    r = twilio.twiml.Response()
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18411459.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18411461.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18433051.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18440995.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18441029.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18467843.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18467869.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18467877.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18497699.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18628819.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18643743.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18687429.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18698497.mp3')
    r.play('https://s3.amazonaws.com/objectphone.cooperhewitt.org/objects/CH-18705685.mp3')

    r.redirect(url="/end-menu", method="GET")
    return str(r)

@app.route("/end-menu")
def endmenu():
    r = twilio.twiml.Response()
    r.say("Thank you for using object phone. Please stay on the line to return to the main menu.")
    r.redirect(url="/", method="GET")

    return str(r)	
	    
            
################ SMS STUFF ###########################
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
    
def subscribe():
    phrase = "Thanks for subscribing. To stop receiving texts, just text STOP."
    return phrase

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
    elif body.lower() == 'subscribe':
        rsp = subscribe()   
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