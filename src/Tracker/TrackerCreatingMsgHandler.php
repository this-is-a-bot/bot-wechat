<?php declare(strict_types = 1);

namespace BotWechat\Tracker;

use BotWechat\Handler\StatefulMsgHandler;
use BotWechat\Handler\WrongMessageForCurrentStateException;


/**
 * Message handler after the server asks the user to create tracking items.
 */
class TrackerCreatingMsgHandler implements StatefulMsgHandler {

  public function handle($message, string $currState) {
    // Only takes text message.
    if ($message->MsgType != 'text') {
      throw new WrongMessageForCurrentStateException(
          "Can only take text message for current state: $currState");
    }

    $user = $message->FromUserName;  // Open ID.
    $content = strtolower($message->Content);
    // Only accepts messages of following format (note they are separated by comma):
    // - 'read xx book, page'.
    $parts = array_map('trim', explode(',', $content));
    if (count($parts) != 2) {
      throw new WrongMessageForCurrentStateException(
          "Message format not recognized for current state: $currState");
    }

    list ($catalogName, $unit) = $parts;
    $resp = Tracker::createTrackingInText($user, $catalogName, $unit);

    return $resp;
  }

}
