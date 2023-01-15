<?php
/*
Plugin Name: (YBPVE) YOURLS Better PeerTube Video Embeds
Plugin URI: https://github.com/spikehidden/YBPVE
Description: Returns a Video Embed for Discord & Co.
Version: 1.0
Author: Spikehidden
Author URI: https://github.com/spikehidden/
LICENSE: MIT License
Support: https://spikey.biz/discord
Donate: https://spikey.biz/kofi
*/
define("PTV_DELAY", "5");
yourls_add_action('pre_redirect','spikey_discord_peertubemeta');

function spikey_discord_peertubemeta($args) {
    $url = $args[0];
    $url2 = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST);
    $base_url = trim($url2, '/');
    
    // Fetch some Metas
    $og = fetch_og($url);
    $platform = $og['platform'];
    $type = $og['type'];
    // Get User Agent
    $agent = $_SERVER["HTTP_USER_AGENT"];

    // Check if it is a bot or site scraper.
    $is_bot = false;
    if (strpos($agent, "facebookexternalhit/") !== false || strpos($agent, "bot") !== false || strpos($agent, "Tool") !== false) {
        $is_bot = true;
    }

    // Check if it is a PeerTube Video
    if ($platform == "PeerTube" && $type == "video" && $is_bot ) {
        // Fetch rest Meta
        $twitter = fetch_twitter($url);
        $metas = get_meta_tags($url);
        $desc = $metas['description'];
        $title = $og['title'];
        $site_name = $og['site_name'];
        // PeerTube API
        $video_ID = basename($og['video:url']);
        $peertube = peertube_stream_url($base_url,$video_ID);
        $stream = $peertube['files'][1]['fileUrl'] ?? null;
        // $channel_name = $peertube['channel']['name'] ?? null;
        // $channel_url = $peertube['channel']['url'] ?? null;
        $author_name = $peertube['channel']['ownerAccount']['name'] ?? null;
        $author_url = $peertube['channel']['ownerAccount']['url'] ?? null;

        // To let it show correctly in Discord
        $oembed = "https://embedl.ink/api/oembed?provider_name=" . $site_name . "&provider_url=" . $base_url . "&author_name=" . $author_name . "&author_url=" . $author_url;
        ?>
            <html>
                <head>
                    <title><?php echo $title . " - " . $site_name; ?></title>
                    <link type="application/json+oembed" href="<?php echo $oembed ?>">
                    <!-- Meta -->
                    <!-- Forward in case a user see this site -->
                    <meta http-equiv="refresh" content="<?php echo PTV_DELAY; ?>; url=<?php echo $url; ?>" />
                    <!-- OG -->
                    <meta property="og:type" content="<?php print "$type"; ?>" />
                    <meta property="og:url" content="<?php echo "$url"; ?>" />
                    <meta property="og:title" content="<?php echo "$title"; ?>" />
                    <meta property="og:video" content="<?php echo "$stream"; ?>" />
                    <meta property="og:video:width" content="1280">
                    <meta property="og:video:height" content="720">
                    <meta property="og:video:type" content="video/mp4" />
                    <meta property="og:site_name" content="<?php echo "$site_name"; ?>" />
                    <meta property="og:image" content="<?php echo $og['image']; ?>"/>
                    <meta property="og:description" content="<?php echo "$desc"; ?>"/>
                    <!-- Normal -->
                    <meta name="description" content="<?php echo "$desc"; ?>"/>
                    <!-- Twitter Card -->
                    <meta property="twitter:card" content="player"/>
                    <meta property="twitter:title" content="<?php echo $twitter['title']; ?>"/>
                    <meta property="twitter:site" content="<?php echo $twitter['site']; ?>"/>
                    <meta property="twitter:description" content="<?php echo $twitter['description']; ?>"/>
                    <meta property="twitter:image" content="<?php echo $twitter['image']; ?>"/>
                    <meta property="twitter:player" content="<?php echo $og['video:url']; ?>"/>
                    <meta property="twitter:creator" content="<?php echo "@" . $author_name; ?>"/>
                    <meta property="twitter:player:width" content="<?php echo $og['video:width']; ?>"/>
                    <meta property="twitter:player:height" content="<?php echo $og['video:height']; ?>"/>
                    <meta property="twitter:player:stream" content="<?php echo "$stream"; ?>"/>
                    <meta property="twitter:secureurl:player_url" content="<?php echo $og['video:url']; ?>"/>
                    
                </head>
                <body>
                    You are being redirected to <a href="<?php echo $url; ?>"><?php echo $url; ?></a>.
                    If you have not been redirected please <a href="<?php echo $url; ?>">Click Here</a>.
                </body>
            </html>
        <?php
        die();
    }
}

function peertube_stream_url($base_url,$id) {
    $api = $base_url . "/api/v1/videos/";

    $url = $api . $id;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $resp = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($resp, true);
    return $data ?? null;
}

// Function from https://gist.github.com/tutweb/8ae422dbb21370ab0715
function fetch_og($url) {
    $data = file_get_contents($url);
    $dom = new DomDocument;
    @$dom->loadHTML($data);
     
    $xpath = new DOMXPath($dom);
    # query metatags dengan prefix og
    $metas = $xpath->query('//*/meta[starts-with(@property, \'og:\')]');

    $og = array();

    foreach($metas as $meta){
        # ambil nama properti tanpa menyertakan og
        $property = str_replace('og:', '', $meta->getAttribute('property'));
        # ambil konten dari properti tersebut
        $content = $meta->getAttribute('content');
        $og[$property] = $content;
    }

    return $og;
}

function fetch_twitter($url) {
    $data = file_get_contents($url);
    $dom = new DomDocument;
    @$dom->loadHTML($data);
     
    $xpath = new DOMXPath($dom);
    # query metatags dengan prefix twitter
    $metas = $xpath->query('//*/meta[starts-with(@property, \'twitter:\')]');

    $twitter = array();

    foreach($metas as $meta){
        # ambil nama properti tanpa menyertakan twitter
        $property = str_replace('twitter:', '', $meta->getAttribute('property'));
        # ambil konten dari properti tersebut
        $content = $meta->getAttribute('content');
        $twitter[$property] = $content;
    }

    return $twitter;
}
