<?php

include_once("../classes.php");

use CharlieRobin\ThrottleViaFileLock;

$throttle = new ThrottleViaFileLock('Test');

$throttle->install();

