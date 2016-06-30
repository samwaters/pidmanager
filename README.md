# PID Manager for PHP
Library to manage PID files in a fail-safe way

## Overview
PID files are used to prevent parallel executions of the same script.  
A PID file is a file containing the Process ID of a currently running process, which is checked when a (usually long-running) script starts up.  
The script will then use the PID file in some way to determine if another instance is already running.

## Project Aims
This project aims to provide a simple, easy to use, drop in manager for PID files. Currently, one line of code is all that's needed.  
It also aims to be extensible, so adding new locking methods is straight forward.

## Locking Methods
### Simple Lock using `flock`
This approach uses file level locking of the PID file to make sure that only one process can use it at any given time.  
If a second instance tries to start and lock the same file, it will fail and the script can assume another instance is already running.  
However, if the process crashes or the machine reboots unexpectedly, file locks may not be released.
#### Simple Lock Usage
`$pidManager = new PidManager("/tmp/test.pid", LockType::SIMPLE);`  
This will create a PidManager using the PID file `/tmp/test.pid`.  
If another instance tries to start, it will fail to get the lock on this file and throw a `ProcessAlreadyRunningException`  
See `Examples/SimpleLockTest.php` for a code example

### Safe Locking
This approach writes the process ID to the PID file and checks it when starting up.

If a process is already running with the process ID in the file, the command of that process will be compared to the current script to see if they're the same.  
If an instance id is specified, this will also be checked against the command to make sure the existing process is not the current script but with a different instance id.  
If the existing process matches the current script, a `ProcessAlreadyRunningException` will be thrown.

#### Safe Lock Usage
`$pidManager = new PidManager("/tmp/test-inst1.pid", LockType::SAFE, basename(__FILE__), "inst1");`  
This will create a PidManager using the PID file `/tmp/test-inst1.pid`, and write the current PID to it.  
If another instance tries to start, this file will be read and the PidManager will check to see if that process is still running, and whether it's the same script.  
If it matches, a `ProcessAlreadyRunningException` will be thrown.
 
See `Examples/SafeLockTest.php` for a code example

## Requirements
* PHP
* *nix host (Windows is not supported due to the need for `ps`)
