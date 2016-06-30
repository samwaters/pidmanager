<?php
/**
 * @author Sam Waters <sam@samwaters.com>
 * @created 29/06/2016 15:52
 * 
 * To run this example:
 * php InstanceUsage.php 123
 * If another instance is already running with the same instance id, an exception will be thrown
 */
use PidManager\PidManager;
use PidManager\Enums\LockType;

include_once("../../../vendor/autoload.php");

if(!isset($argv[1]))
{
  echo "Usage: SafeLockTest.php <instance_id>\n";
  exit;
}
$pidManager = new PidManager("/tmp/InstanceUsage-" . $argv[1] . ".pid", LockType::SAFE, basename(__FILE__), $argv[1]);
echo "Running as instance " . $argv[1] . "\n";
while(true)
{
  echo "Tick\n";
  sleep(5);
}
