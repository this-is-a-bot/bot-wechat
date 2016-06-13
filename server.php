<?php

/*****
 * Copied from https://easywechat.org/zh-cn/docs/tutorial.html
 *****/

include __DIR__ . '/vendor/autoload.php';
include 'steam.php';

use EasyWeChat\Foundation\Application;


$options = [
    'debug'  => true,
    'app_id' => getenv('app_id'),
    'secret' => getenv('secret'),
    'token'  => getenv('token'),

    'log' => [
        'level' => 'debug',
        'file'  => '/tmp/easywechat.log',
    ],
];

$app = new Application($options);
$server = $app->server;


$server->setMessageHandler(function ($message) {
    if ($message->Content=='Steam Discount'){
        $response = get_steam_discounts();
        return $response;
    } else
        return "嘿嘿！";
});
$server->serve()->send();