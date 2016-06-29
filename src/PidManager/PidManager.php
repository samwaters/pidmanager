<?php
/**
 * @author Sam Waters <sam@samwaters.com>
 * @created 29/06/2016 13:48
 */
namespace PidManager;

use PidManager\Exceptions\ProcessAlreadyRunningException;

class PidManager
{
  private $_callingScript;
  private $_instanceId;
  private $_pid;
  private $_pidFile;

  /**
   * PidManager constructor.
   * @param String $pidFile
   * @param String $callingScript
   * @param String|null $instanceId
   * @throws ProcessAlreadyRunningException
   * @throws PidManagerException
   */
  public function __construct($pidFile, $callingScript, $instanceId = null)
  {
    //Make sure we only have the script name
    $callingScriptParts = explode(DIRECTORY_SEPARATOR, $callingScript);
    $callingScript = array_pop($callingScriptParts);
    $callingScript = str_replace(".", "\\.", $callingScript);
    $this->_callingScript = $callingScript;
    $this->_instanceId = $instanceId;
    $this->_pid = getmypid();
    $this->_pidFile = $pidFile;
    if(file_exists($pidFile))
    {
      $existingPid = file_get_contents($pidFile);
      if(is_numeric($existingPid) && $this->isPidRunning($existingPid))
      {
        throw new ProcessAlreadyRunningException("Another instance is already running with PID $existingPid");
      }
    }
    //Validated that there's no process running so we can write the PID file
    $pidHandle = fopen($pidFile, "w");
    if(!$pidHandle)
    {
      throw new PidManagerException("Could not open PID file");
    }
    fwrite($pidHandle, $this->_pid);
    fclose($pidHandle);
  }

  /**
   * @return null|String
   */
  public function getInstanceId()
  {
    return $this->_instanceId;
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

  /**
   * This function checks if the PID in the given PID file is currently running
   * If it is, it checks whether the command is the same as the current script
   * @param String $pid
   * @return bool
   * @throws PidManagerException
   */
  public function isPidRunning($pid)
  {
    if(!is_numeric($pid))
    {
      throw new PidManagerException("Invalid PID");
    }
    //ps -p looks up a specific pid
    //The ps header is always returned, so if there are two lines returned then the pid is running
    //wc -l counts the number of lines
    $isRunning = exec("ps -p $pid | wc -l") == "2";
    if($isRunning)
    {
      //The PID is running, so check if it is this script
      //ps u --pid returns the full command for the specified pid
      //sam      10755  0.0  0.0 167248 18680 pts/6    S+   06:39   0:00 php test.php 12 > out.txt
      $command = exec("ps u -p $pid | tail -n 1");
      //Does the command contain the calling script?
      $pattern = "/" . $this->_callingScript . "(\s.+)?$/";
      if(preg_match($pattern, $command))
      {
        //If the instance id is set, check if that's in the command as well
        if($this->_instanceId != null)
        {
          //Instance id might be test.php 12 or test.php --instanceId=12
          $pattern = "/[\s=]" . $this->_instanceId . "(\s.+)?$/";
          if(preg_match($pattern, $command))
          {
            //The instance id is also present
            return true;
          }
        }
        else
        {
          //No instance id, so we can assume it's running
          return true;
        }
      }

    }
    return false;
  }
}
