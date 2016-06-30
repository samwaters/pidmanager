<?php
/**
 * @author Sam Waters <sam@samwaters.com>
 * @created 29/06/2016 13:48
 */
namespace PidManager;

use PidManager\Enums\LockType;
use PidManager\Exceptions\ProcessAlreadyRunningException;
use PidManager\Managers\SafeLock;
use PidManager\Managers\SimpleLock;

class PidManager
{
  private $_callingScript;
  private $_lockType;
  private $_instanceId;
  private $_pid;
  private $_pidFile;
  private $_pidManager;

  /**
   * PidManager constructor.
   * @param String $pidFile
   * @param String $lockType
   * @param String $callingScript
   * @param String|null $instanceId
   * @throws ProcessAlreadyRunningException
   * @throws PidManagerException
   */
  public function __construct($pidFile, $lockType, $callingScript = null, $instanceId = null)
  {
    if($lockType == LockType::SAFE)
    {
      if($callingScript == null)
      {
        throw new PidManagerException("SafeLock requires a calling script");
      }
      $pidManager = new SafeLock();
      $pidManager->setCallingScript($callingScript);
      $pidManager->setInstanceId($instanceId);
      $pidManager->setPidFile($pidFile);
      $pidManager->lock();
    }
    else
    {
      $pidManager = new SimpleLock();
      $pidManager->setPidFile($pidFile);
      $pidManager->lock();
    }
    $this->_callingScript = $callingScript;
    $this->_instanceId = $instanceId;
    $this->_lockType = $lockType;
    $this->_pid = getmypid();
    $this->_pidFile = $pidFile;
    $this->_pidManager = $pidManager;
  }

  /**
   * @return null|String
   */
  public function getCallingScript()
  {
    return $this->_callingScript;
  }
  
  /**
   * @return null|String
   */
  public function getInstanceId()
  {
    return $this->_instanceId;
  }

  /**
   * @return String
   */
  public function getLockType()
  {
    return $this->_lockType;
  }

  /**
   * @return int
   */
  public function getPid()
  {
    return $this->_pid;
  }

  /**
   * @return string
   */
  public function getPidFile()
  {
    return $this->_pidFile;
  }
  
  public function unlock()
  {
    $this->_pidManager->unlock();
  }
}
