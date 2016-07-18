<?php declare(strict_types = 1);

namespace BotWechat\Tracker;

use BotWechat\Handler\StatefulMsgHandler;
use BotWechat\Handler\WrongMessageForCurrentStateException;


/**
 * Message handler after replying a text list of tracking catalog.
 */
class TrackerListingMsgHandler implements StatefulMsgHandler {

  public function handle($message, string $currState) {
    // Only takes text message.
    if ($message->MsgType != 'text') {
      throw new WrongMessageForCurrentStateException(
          "Can only take text message for current state: $currState");
    }

    $content = strtolower($message->Content);
    // Only accept messages of following formats:
    // - Single number indicating tracking item ID TODO: improve by having another mapping;
    // - Or plus a numeric value.
    $parts = preg_split('/\s+/', $content);
    $catalogID = intval($parts[0]);
    if ($catalogID == 0) {
      throw new WrongMessageForCurrentStateException(
          "Message format not recognized for current state: $currState");
    }

    // 0 will be automatically converted to 1, consider it as a binary mark.
    $val = count($parts) > 1 ? intval($parts[1]) : 0;
    $resp = Tracker::markDone($catalogID, ($val ? $val : 1));

    return $resp;
  }
}
