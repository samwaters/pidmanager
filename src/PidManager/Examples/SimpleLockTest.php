<?php
/**
 * @author Sam Waters <sam@samwaters.com>
 * @created 29/06/2016 15:52
 *
 * To run this example:
 * php SimpleUsage.php
 * If another instance is already running, an exception will be thrown
 */
use PidManager\PidManager;
use PidManager\Enums\LockType;
include_once("../../../vendor/autoload.php");

$pidManager = new PidManager("/tmp/test.pid", LockType::SIMPLE);
echo "Running SimpleUsage test\n";
while(true)
{
  echo "Tick\n";
  sleep(5);
}
