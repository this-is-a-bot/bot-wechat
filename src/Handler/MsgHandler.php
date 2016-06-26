<?php
declare(strict_types=1);

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
    if (preg_match('/steam.*discount/', strtolower($this->message->Content))) {
      return Steam::getSteamDiscounts();
    }

    return new Text(['content' => 'received text: '.$this->message->Content]);
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
