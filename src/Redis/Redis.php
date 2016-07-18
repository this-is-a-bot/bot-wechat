<?php declare(strict_types = 1);

namespace BotWechat\Redis;


use Predis\Client;

/**
 * Format the Redis key to retrieve user state, which is determined by previous reply.
 *
 * Note this should be the same as in bot service, which is responsible to set the state while
 * bot-wechat can only read the state.
 */
function user_state_key(string $user): string {
  return 'bot:user-state:wechat:' . $user;
}


class Redis {

  const USERNAME_TTL = 60 * 60 * 24;  // One day.

  private static $client;

  // MUST be called before using this class. Usually in top-level.
  public static function init() {
    if (getenv('ENV') == 'HEROKU') {
      self::$client = new Client(getenv('REDIS_URL'));
    } else {
      self::$client = new Client([
          'host' => '127.0.0.1',
      ]);
    }
  }

  public static function getPrevState($user): string {
    $k = user_state_key($user);
    return self::$client->get($k) ?? '';  // Conform to type hinting.
  }

}