<?php declare(strict_types = 1);

namespace BotWechat\Handler;


use BotWechat\Tracker\{
    TrackerCreatingMsgHandler, TrackerListingMsgHandler
};
use Exception;


/**
 * Custom exception thrown by stateful message handler.
 */
class WrongMessageForCurrentStateException extends Exception {
}


// Note that all state strings should be the same as in bot service.
class StateManager {
  const NOTHING = 'nothing';
  const TRACKER_LISTING = 'tracker:listing';
  const TRACKING_CREATING = 'tracker:creating';

  private $mapping;

  public function __construct() {
    $this->mapping = [
        self::TRACKER_LISTING => [new TrackerListingMsgHandler()],
        self::TRACKING_CREATING => [new TrackerCreatingMsgHandler()],
    ];
  }

  /**
   * Get a list of stateful handlers corresponding to the given state.
   *
   * @return array A list of stateful message handlers
   */
  public function getStatefulHandlers(string $prevState): array {
    if (!array_key_exists($prevState, $this->mapping)) {
      return [];
    }
    return $this->mapping[$prevState];
  }

}