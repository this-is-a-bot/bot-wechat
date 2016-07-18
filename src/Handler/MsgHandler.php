<?php declare(strict_types = 1);

namespace BotWechat\Handler;

use BotWechat\Steam\Steam;
use BotWechat\Tracker\Tracker;
use EasyWeChat\Message\Text;


/**
 * Entry point for message handling.
 */
class MsgHandler {

  /**
   * First try stateful message handlers with corresponding state. If no correct one exists,
   * turn to stateless ones.
   */
  public static function handleMessage($message, string $prevState) {
    $stateManager = new StateManager();
    /** @var StatefulMsgHandler[] $statefulHandlers */
    $statefulHandlers = $stateManager->getStatefulHandlers($prevState);

    $ret = NULL;
    foreach ($statefulHandlers as $handler) {
      try {
        $ret = $handler->handle($message, $prevState);
        // Once found a legitimate response, break.
        break;
      } catch (WrongMessageForCurrentStateException $e) {
        continue;
      }
    }
    if ($ret) {
      return $ret;
    }

    // Turn to generic stateless handlers if no corresponding stateful handlers.
    $handler = NULL;
    switch ($message->MsgType) {
      case 'text':
        $handler = new TextMsgHandler();
        break;
      case 'voice':
        $handler = new VoiceMsgHandler();
        break;
      case 'image':
        $handler = new ImageMsgHandler();
        break;
      default:
        return new Text(['content' => 'Message type not supported']);
    }

    return $handler->handle($message);
  }

}

/**
 * Interface for message handler with states, implemented under each feature class (such as Tracker).
 */
interface StatefulMsgHandler {
  public function handle($message, string $currState);
}

/**
 * Stateless message handler, which are agnostic about users' states.
 */
interface StatelessMsgHandler {
  public function handle($message);
}

/*
 * Implementation of stateless message handlers.
 */

class TextMsgHandler implements StatelessMsgHandler {
  public function handle($message) {
    $content = $message->Content;
    $user = $message->FromUserName;  // Open ID.

    if (preg_match('/steam.*discount/', strtolower($content))) {
      return Steam::getSteamDiscounts();
    } else if (preg_match('/steam.*feature/', strtolower($content))) {
      // Extract features, default to windows.
      preg_match('/mac|linux|win/', $content, $matches);
      return Steam::getSteamFeaturedGames($matches ? $matches[0] : 'win');
    } else {
      // Trying to recognize the first keyword.
      $parts = preg_split('/\s+/', $content);
      switch (strtolower($parts[0])) {  // Wechat doesn't allow send white-space string.
        case "tt":  // Text-only tracker.
          return Tracker::relayMessage($user, $content);
          break;
        case "t":  // Web-page tracker.
          return Tracker::getTrackerPage($user);
          break;
      }
    }

    return new Text(['content' => 'received text: ' . $content]);
  }
}

class ImageMsgHandler implements StatelessMsgHandler {
  public function handle($message) {
    return new Text(['content' => 'received image: ' . $message->PicUrl]);
  }
}

class VoiceMsgHandler implements StatelessMsgHandler {
  public function handle($message) {
    return new Text(['content' => 'received voice: ' . $message->MediaId]);
  }
}
