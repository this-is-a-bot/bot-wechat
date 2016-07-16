<?php declare(strict_types = 1);

namespace BotWechat\Handler;

use BotWechat\Tracker\Tracker;
use EasyWeChat\Message\Text;
use BotWechat\Steam\Steam;


abstract class MsgHandler {

  protected $message;

  public function __construct($message) {
    $this->message = $message;
  }

  abstract public function handle();

  // TODO: Add state transition. Will do in next diff.
  public static function handleMessage($message, string $prevState) {
    $handler = NULL;
    switch ($message->MsgType) {
      case 'text':
        $handler = new TextMsgHandler($message);
        break;
      case 'voice':
        $handler = new VoiceMsgHandler($message);
        break;
      case 'image':
        $handler = new ImageMsgHandler($message);
        break;
      default:
        return new Text(['content' => 'Message type not supported']);
    }

    return $handler->handle();
  }

}

class TextMsgHandler extends MsgHandler {
  public function handle() {
    $content = $this->message->Content;
    // TODO: Do we need more than open ID for the user, like nickname?
    $user = $this->message->FromUserName;  // Open ID.

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

class ImageMsgHandler extends MsgHandler {
  public function handle() {
    return new Text(['content' => 'received image: ' . $this->message->PicUrl]);
  }
}

class VoiceMsgHandler extends MsgHandler {
  public function handle() {
    return new Text(['content' => 'received voice: ' . $this->message->MediaId]);
  }
}
