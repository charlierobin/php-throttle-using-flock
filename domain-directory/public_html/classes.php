<?php

namespace CharlieRobin;

class ThrottleViaFileLock
{
    protected $limitInSeconds = 5;

    private $identifier = '';

    private $path = '';

    public function __construct($identifier = '')
    {
        if ($identifier == '') throw new \Exception('ThrottleViaFileLock must have an identifier');

        $this->identifier = $identifier;

        $publicHTML = $_SERVER['DOCUMENT_ROOT'];

        $this->path = $publicHTML . '/../CharlieRobinThrottle/CharlieRobinThrottle-' . $this->identifier . '.txt';
    }

    public function install()
    {
        $publicHTML = $_SERVER['DOCUMENT_ROOT'];

        if (!file_exists($publicHTML . '/../CharlieRobinThrottle')) {

            $success = mkdir($publicHTML . '/../CharlieRobinThrottle', 0777, true);

            if (!$success) throw new \Exception('ThrottleViaFileLock could not create CharlieRobinThrottle directory');
        }

        if (!file_exists($this->path)) {

            $f = fopen($this->path, 'w');

            $last = '2001-01-01 00:00:00';

            fwrite($f, $last);

            fclose($f);
        }
    }

    public function lockThenSleepThenUnlock($duration = 10, $logging = false)
    {
        if (!file_exists($this->path)) {

            error_log('ThrottleViaFileLock: please run install.php script (or your own equivalent, or set up manually)', 0);

            return;
        }

        // beware duration > max_execution_time

        $max_execution_time = floatval(ini_get('max_execution_time'));

        if ($duration >= $max_execution_time) {

            if ($logging) error_log('duration >= max_execution_time, setting to max_execution_time - 5', 0);

            $duration = $max_execution_time - 5;
        }

        $f = fopen($this->path, 'r');

        if (!$f) {

            if ($logging) error_log('no $f', 0);

            return;
        }

        if (flock($f, LOCK_EX | LOCK_NB)) {

            if ($logging) error_log($duration . ' second flock starting', 0);

            $count = 0;

            while ($count < $duration) {

                if ($logging) error_log('flock sleep: ' . $count, 0);

                sleep(1);

                $count++;
            }

            flock($f, LOCK_UN);

            if ($logging) error_log($duration . ' second flock sleep done', 0);
        } else {

            if ($logging) error_log('flock failed', 0);
        }

        fclose($f);
    }

    public function allowed()
    {
        if (!file_exists($this->path)) {

            error_log('ThrottleViaFileLock: please run install.php script (or your own equivalent, or set up manually)', 0);

            return false;
        }

        $f = fopen($this->path, 'r+');

        if (!$f) return false;

        $allowed = false;

        if (flock($f, LOCK_EX | LOCK_NB)) {

            $date = new \DateTime('now');

            $dateAsString = $date->format('Y-m-d H:i:s');

            $last = '2001-01-01 00:00:00';

            $last = fgets($f);

            $timeLast  = strtotime($last);

            $timeNow = strtotime($dateAsString);

            $differenceInSeconds = $timeNow - $timeLast;

            if ($differenceInSeconds > $this->limitInSeconds) $allowed = true;

            $result = fseek($f, 0, SEEK_SET);

            fwrite($f, $dateAsString);

            flock($f, LOCK_UN);
        }

        fclose($f);

        return $allowed;
    }
}
