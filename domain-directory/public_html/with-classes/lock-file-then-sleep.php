<?php

include_once("../subclasses.php");

use CharlieRobin\ThrottleViaFileLock;

$duration = 5;

if (isset($_GET['duration'])) $duration = floatval($_GET['duration']);

// beware duration > max_execution_time, 30 on my test system

$max_execution_time = floatval(ini_get('max_execution_time'));

if ($duration >= $max_execution_time) $duration = $max_execution_time - 5;

$throttle = new ThrottleViaFileLock('Test');

// duration of test lock in seconds, enable logging as boolean

$throttle->lockThenSleepThenUnlock($duration, true);
