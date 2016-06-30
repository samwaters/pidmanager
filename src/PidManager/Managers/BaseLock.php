<?php
/**
 * @author Sam Waters <sam@samwaters.com>
 * @created 30/06/2016 14:09
 */
namespace PidManager\Managers;

abstract class BaseLock
{
  protected $_pidFile;
  
  abstract public function lock();
  abstract public function unlock();
  
  public function setPidFile($pidFile)
  {
    $this->_pidFile = $pidFile;
  }
}
