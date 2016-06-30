<?php
/**
 * @author Sam Waters <sam@samwaters.com>
 * @created 30/06/2016 13:57
 */
namespace PidManager\Managers;

use PidManager\Exceptions\ProcessAlreadyRunningException;

class SimpleLock extends BaseLock
{
  protected $_pidFileHandle;

  public function lock()
  {
    $this->_pidFileHandle = fopen($this->_pidFile, "r+");
    if(flock($this->_pidFileHandle, LOCK_EX | LOCK_NB))
    {
      ftruncate($this->_pidFileHandle, 0);
      fwrite($this->_pidFileHandle, getmypid());
    }
    else
    {
      throw new ProcessAlreadyRunningException("Another instance is already running");
    }
  }
  
  public function unlock()
  {
    fflush($this->_pidFileHandle);
    flock($this->_pidFileHandle, LOCK_UN);
    fclose($this->_pidFile);
  }
}
