<?php declare(strict_types = 1);

namespace BotWechat\Tracker;

use BotWechat\Handler\StatefulMsgHandler;
use BotWechat\Handler\StateManager;
use BotWechat\Handler\WrongMessageForCurrentStateException;
use BotWechat\Redis\Redis;


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

    $user = $message->FromUserName;  // Open ID.
    $content = trim(strtolower($message->Content));
    // Only accept messages of following formats:
    // - String 'new' or 'n' to create new tracking catalogs;
    // - Single number indicating tracking item ID TODO: improve by having another mapping;
    // - Or plus a numeric value.

    if ($content == 'new' || $content == 'n') {
      Redis::setPrevState($user, StateManager::TRACKING_CREATING);
      return Tracker::INSTRUCTION_CREATING;
    }

    $parts = preg_split('/\s+/', $content);
    $catalogID = intval($parts[0]);
    if ($catalogID == 0) {
      throw new WrongMessageForCurrentStateException(
          "Message format not recognized for current state: $currState");
    }

    // 0 means no value is needed, consider it as a binary mark.
    $val = count($parts) > 1 ? ((float) $parts[1]) : 0.0;
    $resp = Tracker::markDoneInText($user, $catalogID, $val);

    return $resp;
  }

}
