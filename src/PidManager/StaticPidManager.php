<?php
/**
 * @author Sam Waters <sam@samwaters.com>
 * @created 29/06/2016 13:49
 */
namespace PidManager;

class StaticPidManager
{
  /** @var PidManager $_pidManager */
  private static $_pidManager;
  
  public static function create($pidFile, $callingScript, $instanceId = null)
  {
    self::$_pidManager = new PidManager($pidFile, $callingScript, $instanceId);
  }

  /**
   * @return PidManager
   */
  public static function getPidManager()
  {
    return self::$_pidManager;
  }
}
