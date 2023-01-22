<?php
namespace Pipe\Cache\Storage;

use Pipe\Exception\Exception;
use Pipe\StdLib\IConfigurable;

class Filesystem implements IStorage, IConfigurable
{
  protected array $config = [];
  public function setConfig($config)
  {
    if(!is_dir($config['folder'])) {
      throw new Exception('Folder "' . $config['filepath'] . '" not found');
    }
    $this->config = $config;
  }

  protected function getFilename($key): string
  {
    return $this->config['folder'] . '/' . $key . '.txt';
  }

  public function save($key, $string): bool
  {
    return (bool) @file_put_contents($this->getFilename($key), $string);
  }

  public function load($key): string
  {
    return (string) @file_get_contents($this->getFilename($key));
  }

  public function remove($key): bool
  {
    return @unlink($this->getFilename($key));
  }

  public function has($key): bool
  {
    return file_exists($this->getFilename($key));
  }
}