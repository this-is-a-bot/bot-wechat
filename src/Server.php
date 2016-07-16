<?php declare(strict_types = 1);

namespace BotWechat;

include __DIR__ . '/../vendor/autoload.php';

use BotWechat\Handler\MsgHandler;
use BotWechat\Redis\Redis;
use EasyWeChat\Foundation\Application;

$options = [
    'debug' => true,
    'app_id' => getenv('app_id'),
    'secret' => getenv('secret'),
    'token' => getenv('token'),

    'log' => [
        'level' => 'debug',
        'file' => './easywechat.log',
    ],
];

// Init Redis.
Redis::init();

// Init easywechat API.
$app = new Application($options);
$server = $app->server;

$server->setMessageHandler(function ($message) use ($app) {
  $username = $message->FromUserName;
  $prevState = Redis::getPrevState($username);
  return MsgHandler::handleMessage($message, $prevState);
});

$server->serve()->send();
