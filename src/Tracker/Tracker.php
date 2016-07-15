<?php declare(strict_types = 1);

namespace BotWechat\Tracker;

use EasyWeChat\Message\{
    News, Text
};


/**
 * Support tracking users' interests or personal goals set by themselves.
 * Support two types of tracker display: 1. by web page; 2. by texts.
 */
class Tracker {

  const APP = 'wechat';

  public static function relayMessage(string $user, string $message) {
    // TODO: Relay message to bot service, and return the response string (empty if error).
    return new Text(['content' => 'relayMessage not implemented, ' . $user]);
  }

  public static function getTrackerPage(string $user) {
    // TODO: Call bot endpoint to fetch the web page url (empty if error).
    return new News([
      'title' => 'Your Tracker!',
      'description' => 'Description for the tracker',
      'url' => '',
    ]);
  }
}
