<?php
include __DIR__ . '/../vendor/autoload.php';
function d($data = '') { echo '<pre>' . "\n" . var_export($data, true) . "\n" . '</pre>' . "\n"; }
function dd($data = 'END') { die(d($data)); }

$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

define('BASEDIR', realpath(__DIR__ . '/Pipe'));
error_reporting(E_ERROR);
?>



