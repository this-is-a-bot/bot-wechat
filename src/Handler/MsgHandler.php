<?php declare(strict_types=1);

namespace BotWechat\Handler;

use EasyWeChat\Message\Text;
use BotWechat\Steam\Steam;


abstract class MsgHandler {

  protected $message;

  public function __construct($message) {
    $this->message = $message;
  }

  abstract public function handle();

  public static function handleMessage($message) {
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
    if (preg_match('/steam.*discount/', strtolower($content))) {
      return Steam::getSteamDiscounts();
    } else if (preg_match('/steam.*feature/', strtolower($content))) {
      // Extract features, default to windows.
      preg_match('/mac|linux|win/', $content, $matches);
      return Steam::getSteamFeaturedGames($matches ? $matches[0] : 'win');
    }

    return new Text(['content' => 'received text: '.$content]);
  }
}

class ImageMsgHandler extends MsgHandler {
  public function handle() {
    return new Text(['content' => 'received image: '.$this->message->PicUrl]);
  }
}

class VoiceMsgHandler extends MsgHandler {
  public function handle() {
    return new Text(['content' => 'received voice: '.$this->message->MediaId]);
  }
}
