<?php

include_once("classes.php");

use CharlieRobin\ThrottleViaFileLock;

class MyTestThrottle extends ThrottleViaFileLock
{
    public function __construct()
    {
        parent::__construct('Test');

        $this->limitInSeconds = 2;
    }

    public function lockTest()
    {
        parent::lockThenSleepThenUnlock(10, true);
    }
}
