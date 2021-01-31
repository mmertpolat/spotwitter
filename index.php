<?php

require 'vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

// Enter your credentials here //

$spotifyClientId = ""; // get from developer.spotify.com
$spotifyClientSecret = ""; // get from developer.spotify.com
$spotifyRedirectUri = "http://localhost/spotwitter/index.php"; // get from developer.spotify.com

$twitterConsumer = ""; // get from developer.twitter.com, needs developer account
$twitterConsumerSecret = ""; // get from developer.twitter.com, needs developer account
$twitterAccessToken = ""; // get from developer.twitter.com, needs developer account
$twitterAccessTokenSecret = ""; // get from developer.twitter.com, needs developer account

$standardbio = "your standard bio"; // if spotify is closed, update bio

// Enter your credentials here //

$session = new SpotifyWebAPI\Session(
    $spotifyClientId,
    $spotifyClientSecret,
    $spotifyRedirectUri
);

$api = new SpotifyWebAPI\SpotifyWebAPI();

if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $api->setAccessToken($session->getAccessToken());

    $options = [
        'scope' => [
            'user-read-email',
            'user-read-currently-playing',
            'user-read-playback-state'
        ],
    ];

    $calansarki = $api->getMyCurrentPlaybackInfo($options);

    $value = json_decode(json_encode($calansarki), true);

    // if no music, update standard bio //

    if(!isset($value['item']['album']['artists'][0]['name'])){
        echo "No Music";
        $connection = new TwitterOAuth($twitterConsumer, $twitterConsumerSecret, $twitterAccessToken, $twitterAccessTokenSecret);

        $statues = $connection->post("account/update_profile", ["description" => $standardbio]);
        exit;
    }

    // if no music, update standard bio end //

    // if spotify is online //

    $sarki = $value['item']['album']['artists'][0]['name']." - ".$value['item']['name'];
    echo $sarki;

    // if spotify is online //

    // twitter bio song update //

    $connection = new TwitterOAuth($twitterConsumer, $twitterConsumerSecret, $twitterAccessToken, $twitterAccessTokenSecret);

    $statues = $connection->post("account/update_profile", ["description" => "🎧 $sarki"]);

    // twitter bio song update end //

} else {
    $options = [
        'scope' => [
            'user-read-email',
            'user-read-currently-playing',
            'user-read-playback-state'
        ],
    ];

    header('Location: ' . $session->getAuthorizeUrl($options));
    die();
}

?>