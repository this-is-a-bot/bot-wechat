<?php

/*****
 * Copied from https://bot-wechat-edfward.c9users.io/
 *****/

include __DIR__ . '/vendor/autoload.php'; // 引入 composer 入口文件

use EasyWeChat\Foundation\Application;

$options = [
    'debug'  => true,
    'app_id' => 'your-app-id',
    'secret' => 'you-secret',
    'token'  => 'easywechat',

    // 'aes_key' => null, // 可选

    'log' => [
        'level' => 'debug',
        'file'  => '/tmp/easywechat.log', // XXX: 绝对路径！！！！
    ],

    //...
];

$app = new Application($options);

$server->setMessageHandler(function ($message) {
    return "您好！欢迎关注我!";
});

$response = $app->server->serve();

// 将响应输出
return $response; //其它框架：$response->send();
