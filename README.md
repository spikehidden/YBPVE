# YBPVE
 > **Y**OURLS **B**etter **P**eerTube **V**ideo **E**mbeds

 Provides several Meta Tags and Data for Discord and othe Site Scrapers to correctly Embed Videos from PeerTube instances.

 You can try it out by sending "[`https://spikey.biz/pt22`](https://spikey.biz/pt22)" in Discord.
 
 ## Requirements
 * PHP 7.4+ (8.2 compatible)
 * YOURLS 1.8.2+ (1.9.2 compatible) (might work with older versions 🤷‍♂️)
 * CURL extension for PHP
 
 ## Screenshots
 ![Screenshot](/images/screenshot.png)
 ![Screenshot](/images/screenshot2.png)

 ## Config
 There's only one thing you can set up at the moment at that is the delay of when a normal user visits a peertube short link (even though this shouldn't happen...) gets redirected.\
 Default is 5 seconds. If you want to change this you need to add the following line at the end of your YOURLS config.php:

 ```php
 define('PTV_DELAY','5');
 ```

Then replace the "5" with the time in seconds you would prefer.\
For no delay you can use "0", though this can also cause sometimes to not automaticly redirect at all.

## TO-DO
- Site compatibility
  - [x] Discord
  - [x] Twitter (Twitter does only embed videos from whitelisted platforms.)
  - [x] Mastodon
  - [ ] Facebook (Does it work already?)
  - [ ] more? (Make a request vie Github issue!)
- [ ] Adding possibility to block certain instances.
- [ ] Adding the possibility to block NSFW videos.
- [ ] Adding the possibility to show a Warning before redirecting to NSFW videos.
- [ ] Adding a check to show the correct Source if link is not from the Original Instance

 ## Alternatives
 If you prefer to generate on the fly embeds you should check out [TubeFix](https://github.com/spikehidden/TubeFix).