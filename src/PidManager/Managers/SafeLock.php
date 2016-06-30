<?php
/**
 * @author Sam Waters <sam@samwaters.com>
 * @created 30/06/2016 13:59
 */
namespace PidManager\Managers;

use PidManager\Exceptions\ProcessAlreadyRunningException;
use PidManager\PidManagerException;

class SafeLock extends BaseLock
{
  protected $_callingScript;
  protected $_instanceId;
  
  protected function _isPidRunning($pid)
  {
    if(!is_numeric($pid))
    {
      throw new PidManagerException("Invalid PID");
    }
    //ps -p looks up a specific pid
    $isRunning = exec("ps -p $pid | wc -l") == "2"; //ps header is always returned so there will always be at least one line
    if($isRunning)
    {
      //ps u --pid returns the full command for the specified pid
      //sam      10755  0.0  0.0 167248 18680 pts/6    S+   06:39   0:00 php test.php 12 > out.txt
      $command = exec("ps u -p $pid | tail -n 1");
      $pattern = "/" . $this->_callingScript . "(\s.+)?$/"; //Regex to see if the calling script is in the command
      if(preg_match($pattern, $command))
      {
        //If the instance id is set, check if that's in the command as well
        if($this->_instanceId != null)
        {
          //Instance id might be test.php 12 or test.php --instanceId=12
          $pattern = "/[\s=]" . $this->_instanceId . "(\s.+)?$/";
          if(preg_match($pattern, $command))
          {
            return true; //Instance id is also present
          }
        }
        else
        {
          return true; //No instance id, so we can assume it's running
        }
      }
    }
    return false;
  }

  public function lock()
  {
    if(file_exists($this->_pidFile))
    {
      $existingPid = file_get_contents($this->_pidFile);
      if(is_numeric($existingPid) && $this->_isPidRunning($existingPid))
      {
        throw new ProcessAlreadyRunningException("Another instance is already running with PID $existingPid");
      }
    }
    //Validated that there's no process running so we can write the PID file
    $pidHandle = fopen($this->_pidFile, "w");
    if(!$pidHandle)
    {
      throw new PidManagerException("Could not open PID file for writing");
    }
    fwrite($pidHandle, getmypid());
    fclose($pidHandle);
  }

  public function setCallingScript($callingScript)
  {
    $callingScriptParts = explode(DIRECTORY_SEPARATOR, $callingScript);
    $callingScript = array_pop($callingScriptParts);
    $callingScript = str_replace(".", "\\.", $callingScript);
    $this->_callingScript = $callingScript;
  }

  public function setInstanceId($instanceId)
  {
    $this->_instanceId = $instanceId;
  }
  
  public function unlock()
  {
    
  }
}
