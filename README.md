# php-throttle-using-flock

Utility class to help limit the number of times a PHP script can be called, by setting and enforcing a minimum time (in seconds) allowed between calls

For throttling a PHP script being used as AJAX endpoint: protecting an email function from being bombarded etc

A more detailed README will be on the way, but in the meanwhile ...

A `CharlieRobinThrottle` directory is required at the same level as the webroot directory,

ie:

the-server/
   domain-directory/
      public_html/
      logs/
      public_ftp/

      (etc etc depending on your setup)

... becomes ...

the-server/
   domain-directory/
      public_html/
      logs/
      public_ftp/
      CharlieRobinThrottle/
         CharlieRobinThrottle-uniqueIdentifer.txt

Each throttled service must its own unique identifier, and uses a file together with PHP’s fopen/flock as persistence between different PHP executions.

In other words, if you are throttling just one thing, there will be just one file in `CharlieRobinThrottle`: `CharlieRobinThrottleIdentifer1.txt`.

But if you are throttling two things, with different allowed intervals between them etc, then there will be `CharlieRobinThrottleIdentifer1.txt` and `CharlieRobinThrottleIdentifer2.txt`.

(You might want to fiddle with the default directory and base filenames that are created/used, but these are the ones that were good enough for me, so that’s what is in this repo.)

## (1) Install

Using the provided `install.php` script, or manually using your own FTP or other server files manager.

## (2) Create instance of `ThrottleViaFileLock` class and use it:

```
<?php

include_once("../classes.php");

use CharlieRobin\ThrottleViaFileLock;

$throttle = new ThrottleViaFileLock('Test');

if ($throttle->allowed()) {

    // enough time has passed since last check: we can go ahead and do whatever it was that we wanted to do
    
} else {

    // not enough time has paased: deny the request, or buffer it, or whatever your strategy is for dealing with this situation
}
```
In the example above, the unique id is `Test`, so the file needed in the `CharlieRobinThrottle` directory would be `CharlieRobinThrottleTest.txt`.

Each call to `allowed()` updates the time recorded in the `flock` file. An alternative (and perhaps better) would be that it’s only updated on a success, not on a failure. But again, this is the way I wanted it for myself. If you need something different, it’s simple enough to change. (In the `allowed()` method of the `ThrottleViaFileLock` class definition.)

## (3) Alternatively, create your own subclass of `ThrottleViaFileLock` and use that:

```
<?php

include_once("../subclasses.php");

$throttle = new MyTestThrottle();

if ($throttle->allowed()) {

    // go right ahead ...

} else {

    // too soon!

}
```

All of this works on my local MAMP Pro install using Apache, and works on the live site it was created for (which I think is Nginx, but I’ll have to double check that).

Beyond that, I’m not sure what the situation is with Windows or any other flavour/combinations of server software.







