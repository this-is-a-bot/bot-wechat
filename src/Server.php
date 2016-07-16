<?php declare(strict_types = 1);

namespace BotWechat;

include __DIR__ . '/../vendor/autoload.php';

use BotWechat\Handler\MsgHandler;
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

$app = new Application($options);
$server = $app->server;

$server->setMessageHandler(function ($message) {
  return MsgHandler::handleMessage($message);
});
$server->serve()->send();
