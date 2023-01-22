<?php


$cache = \Pipe\Cache\CacheController::getInstance();
$cache->setConfig([
  'storages' => [
    \Pipe\Cache\Storage\Filesystem::class => [
      'folder'  => __DIR__ . '/cache',
    ],
    \Pipe\Cache\Storage\APCu::class => [
      'ttl'  => 60*60,
    ],
  ],
  'filters' => [

  ],
]);

$key = '';
$obj = '';

d($cache->getStorage()::class);

//$cache->has('key');
$start = microtime(true);
for($i = 0; $i < 1500; $i++) {
  $cache->set('key', intval($cache->get('key')) + 1);
  $cache->get('key');
}
$time_elapsed_secs = microtime(true) - $start;

dd($time_elapsed_secs);
