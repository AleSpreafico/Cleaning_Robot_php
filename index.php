#!/usr/bin/php


<?php

require './vendor/autoload.php';

use Plugins\MyQ\CleaningRobot\MyQCleaningRobot;

$input = $argv[1];
$output = $argv[2];


$json_input = file_get_contents($input);

$json = json_decode($json_input, true);

// TODO: remember to validate!!

$user_cleaning_robot = new MyQCleaningRobot($json, $output);
$user_cleaning_robot->start();


// var_dump($json);


// echo '<pre>' . print_r($json, true) . '</pre>';

// echo $json;
