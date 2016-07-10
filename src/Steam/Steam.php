<?php declare(strict_types=1);

namespace BotWechat\Steam;

use EasyWeChat\Message\News;


class Steam {

    const URL = 'http://a-b0t.herokuapp.com';

    public static function getSteamDiscounts(): array {
        $endpoint = self::URL . '/steam/discounts';
        $raw_response = file_get_contents($endpoint);
        $games = json_decode($raw_response, true);
        $responses = [];
        foreach ($games as &$game) {
            array_push($responses, self::wrapGame($game));
        };
        return array_slice($responses, 0, 5);
    }

    public static function getSteamFeaturedGames(string $feature): array {
        $endpoint = self::URL . '/steam/featured';
        if ($feature) {
            $endpoint .= '?feature=' . $feature;
        }
        $raw_response = file_get_contents($endpoint);
        $games = json_decode($raw_response, true);
        $responses = [];
        foreach ($games as &$game) {
            array_push($responses, self::wrapGame($game));
        };
        return array_slice($responses, 0, 5);
    }

    private static function wrapGame(array $game) {
        return new News([
                'title'       => $game['name'] . '($'. $game['priceBefore'] . '->$' . $game['priceNow'] . ')',
                'description' => $game['review'],
                'url'         => $game['url'],
                'image'       => $game['imgSrc']
        ]);
    }

}
