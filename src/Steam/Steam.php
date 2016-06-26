<?php
declare(strict_types=1);

namespace BotWechat\Steam;

use EasyWeChat\Message\News;


class Steam {

    public static function getSteamDiscounts(): array {
        $url = 'http://a-b0t.herokuapp.com/steam/discounts';
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

}