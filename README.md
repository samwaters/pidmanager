# PID Manager for PHP
Library to manage PID files in a fail-safe way

## Overview
PID files are used to prevent parallel executions of the same script.

A PID file is a file containing the Process ID of a currently running process, which is checked when a (usually long-running) script starts up.

The script will then use the PID file in some way to determine if another instance is already running.

## Traditional Approaches
### Lock the file using `flock`
This approach uses file level locking of the PID file to make sure that only one process can use it at any given time.

If a second instance tries to start and lock the same file, it will fail and the script can assume another instance is already running.

However, if the process crashes or the machine reboots unexpectedly, file locks may not be released. 

### Store the ID and check if the process is already running
This approach stores the script's unique process id (PID) in the file.

When a script starts up, it checks to see if the PID file exists. If it does, it checks to see if a process with that ID is currently running.

Whilst PIDs are not re-used in normal cases, if the machine reboots they will start again. This can mean that the same PID may be used for an unrelated process.

## Aims of this project
This project aims to manage PIDs at a process level, rather than relying on file locks and process IDs.

It works by writing the process ID to a file, then checking what that process ID actually is (if it exists) when starting a script.

If the process ID does exist, it compares the command (and instance id if there is one) to make sure it's not a false positive.

## Usage
### Simple Usage
`$pidManager = new PidManager("/tmp/test.pid", basename(__FILE__));`

This will create a PidManager using the PID file `/tmp/test.pid`, for the current script.

If an instance is already running, it will throw a `ProcessAlreadyRunningException` exception to be handled as required.

See `Examples/SimpleUsage.php` for a code example

### Usage with instance IDs
`$pidManager = new PidManager("/tmp/test-inst1.pid", basename(__FILE__), "inst1");`

This will create a PidManager using the PID file `/tmp/test-inst1.pid` for the current script.

If the process ID exists, the command will be compared as above, but the instance ID will also be checked.
 
This allows for multiple instances to be run in parallel, as long as they have different instance IDs.

See `Examples/InstanceUsage.php` for a code example

## Requirements
* PHP
* *nix host (Windows is not supported due to the need for `ps`)
