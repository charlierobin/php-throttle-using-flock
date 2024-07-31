<?php

include_once("../subclasses.php");

$throttle = new MyTestThrottle();

if ($throttle->allowed()) {

    $data = file_get_contents('php://input');

    $data = json_decode($data, true);

    // this is just a test endpoint, so we send whatever JSON was submitted back, together with result => OK

    $data = array("result" => "OK", "data" => $data);
} else {

    // if not allowed, due to problem with install, or not enough time inbetween calls (limitInSeconds),
    // then we send back result => FAILED, and it's up to the client to decide what to do,
    // eg: wait a few seconds and then try again, perhaps, or some other strategy

    $data = array("result" => "FAILED");
}

header('Content-Type: application/json; charset=utf-8');

echo json_encode($data);
