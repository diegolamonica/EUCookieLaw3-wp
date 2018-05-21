# EUCookieLaw 3 WordPress Plugin

>  EUROPA websites must follow the Commission's guidelines on [privacy and data protection](http://ec.europa.eu/ipg/basics/legal/data_protection/index_en.htm) and inform 
  users that cookies are not being used to gather information unnecessarily.
   
>  The [ePrivacy directive](http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=CELEX:32002L0058:EN:HTML) – more specifically Article 5(3) – requires prior informed consent for storage for access to information stored on a user's terminal equipment. 
  In other words, you must ask users if they agree to most cookies and similar technologies (e.g. web beacons, Flash cookies, etc.) before the site starts to use them.

>  For consent to be valid, it must be informed, specific, freely given and must constitute a real indication of the individual's wishes.

In this context this solution lives.
It simply alters the default `document.cookie` behavior to disallow cookies to be written on the client side, until the user accept the agreement.
At the same time it blocks all third-party domains you have configured as cookie generators.

# Donations

If you find this script useful, and since I've noticed that nobody did this script before of me,
I'd like to receive [a donation](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40diegolamonica%2einfo&lc=IT&item_name=EU%20Cookie%20Law%203&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest). :)


# About this library

**This repository contains the EUCookieLaw WordPress plugin version.** 
There is a [Javascript version](https://www.github.com/diegolamonica/EUCookieLaw3) too. 

The purpose of EUCookieLaw3 is to grant a simple solution to comply the 
[ePrivacy directive](http://eur-lex.europa.eu/LexUriServ/LexUriServ.do?uri=CELEX:32002L0058:EN:HTML) – concerning the cookie consent and General Data Protection Rules (also known as **GDPR**).

There is an older version of this library named just [EUCookieLaw](https://www.github.com/diegolamonica/eucookielaw/) which this one is the direct evolution.

It was developed as new one due the several breaking changes in it.

# How to use it

1. Download the entire repository as a zip archive and install it into your WordPress as you did with other plugins.

2. Else you can install it via [WordPress plugin repository](#) (awaiting for approval).
  
3. To create a banner please use the [online configuration builder](https://diegolamonica.info/tools/eucookielaw/builder/) 

# Available shortcodes

* `[review_button]` allows you to show a button on the page which the user can use to reconsider the consents/rejects previously given.

  This shortcode expects 2 arguments:
  * `title` the button text.
  * `class` one or more CSS classes to apply to the item. 

* `[show_consents]` this shortcode builds a list of choiches about the consents and rejections of the user.
