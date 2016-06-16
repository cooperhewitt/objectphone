## objectphone

It's our API, on your phone. Object Phone is a simple app that uses Twilio to allow you to 'listen' to our collections metadata. I originally [posted a blog on Cooper-Hewitt Labs](http://labs.cooperhewitt.org/2013/object-phone/), but it has matured a bit since that post.

If you'd like to sign up for daily objects, just visit [objectphone.cooperhewitt.org](http://objectphone.cooperhewitt.org)

### version 2

Version 2 of objectphone take a slightly more complex approach to the same simple problem of giving a voice to the Cooper Hewitt Collection. 

In version two, a number of feature requests and new ideas prompted me to switch to using Flamework instead of Python/Flask as the base framework. This is laregely due to the desire to one day migrate some of this code into our production environment, and make objectphone a "real thing." Additionally, I just really like working on Flamework and have become pretty comfortable with it over the years.

New features in version 2 will include:

1. Ability to subscribe to daily objects ( this already works )
2. Ability to like things or dislike things, with the intention being more personalized daily objects.
3. Ability to "collect" things and add them to a user's shoebox on the collection website.
4. Ability to "ask" things, generating a Slack message that our staff can easily respond to.

Additioanlly, it's probably time to start thinking of how we can allow custom content/menu organzation to be managed outside of the codebase. Still thinking of the simplest, dumbest thing to approach that problem.

for the time being, both versions will exist in parallel, using two different phone numbers.

### version 1

[original python version](https://github.com/cooperhewitt/objectphone/tree/python-phone)


