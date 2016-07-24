<?php declare(strict_types = 1);

namespace BotWechat\Tracker;

use BotWechat\Handler\StateManager;
use BotWechat\Redis\Redis;
use EasyWeChat\Message\{
    News
};


/**
 * Support tracking users' interests or personal goals set by themselves.
 * Support two types of tracker display: 1. by web page; 2. by texts.
 */
class Tracker {

  const APP = 'wechat';
  const URL = 'http://a-b0t.herokuapp.com';
  const INSTRUCTION_CREATING =
      "Track something by typing '<name>, <(optional) unit>'\n" .
      'For example, "weight, kg" or "read a book, page"';

  private static $client = NULL;

  private static function getClient() {
    if (!self::$client) {
      self::$client = new \GuzzleHttp\Client([
          'base_uri' => self::URL,
          'http_errors' => false,
      ]);
    }
    return self::$client;
  }

  public static function showTrackingInText(string $user) {
    $c = self::getClient();
    $r = $c->get('/tracker/listing/text', [
        'query' => ['username' => $user, 'app' => self::APP],
    ]);

    $ret = NULL;
    switch ($r->getStatusCode()) {
      case 200:
        Redis::setPrevState($user, StateManager::TRACKER_LISTING);
        $ret = (string) $r->getBody();
        break;
      case 404:
        Redis::setPrevState($user, StateManager::TRACKING_CREATING);
        $ret = self::INSTRUCTION_CREATING;
        break;
      default:
        $ret = 'ERROR: ' . (string) $r->getBody();
    }
    return $ret;
  }

  public static function getTrackerPage(string $user) {
    // TODO: Call bot endpoint to fetch the web page url (empty if error).
    return new News([
        'title' => 'Your Tracker!',
        'description' => 'Description for the tracker',
        'url' => '',
    ]);
  }

  public static function createTrackingInText(string $user, string $name, string $unit) {
    $c = self::getClient();
    $r = $c->post('/tracker/listing/text', [
        'form_params' => [
            'username' => $user, 'app' => self::APP, 'name' => $name, 'unit' => $unit
        ]
    ]);

    $ret = NULL;
    switch ($r->getStatusCode()) {
      case 200:
        // Creation succeeds. Back to listing state.
        Redis::setPrevState($user, StateManager::TRACKER_LISTING);
        $ret = (string) $r->getBody();
        break;
      default:
        $ret = 'ERROR: ' . (string) $r->getBody();
    }
    return $ret;
  }

  public static function markDoneInText(string $user, int $catalogID, float $value): string {
    $c = self::getClient();
    $r = $c->post('/tracker/marking/text', [
        'form_params' => [
            'username' => $user, 'app' => self::APP,
            'catalogID' => $catalogID, 'value' => $value
        ]
    ]);

    $ret = NULL;
    switch ($r->getStatusCode()) {
      case 200:
        // Marking succeeds. Back to listing state.
        Redis::setPrevState($user, StateManager::TRACKER_LISTING);
        $ret = (string) $r->getBody();
        break;
      case 404:
        // Somehow didn't find the created tracking.
        Redis::setPrevState($user, StateManager::TRACKING_CREATING);
        $ret = self::INSTRUCTION_CREATING;
        break;
      default:
        $ret = 'ERROR: ' . (string) $r->getBody();
    }
    return $ret;
  }
}
