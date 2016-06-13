<?php

use EasyWeChat\Message\News;

/**
 * Return Steam discount information.
 *
 * 
 */
function get_steam_discounts()
{
    $url = 'http://a-b0t.herokuapp.com/steam/discounts';
    //return $response;
    $raw_response = file_get_contents($url);
    $games = json_decode($raw_response, true);
    $responses = array();
    foreach ($games as &$game) {
        $new = new News([
            'title'       => $game['name'] . '($'. $game['priceBefore'] . '->$' . $game['priceNow'] . ')',
            'description' => $game['review'],
            'url'         => $game['url'],
            'image'       => $game['imgSrc']
        ]);
        array_push($responses, $new);
    };
    return array_slice($responses, 0, 5);
}