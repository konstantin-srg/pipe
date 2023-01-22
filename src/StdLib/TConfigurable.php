<?php
namespace Pipe\StdLib;

trait TConfigurable
{
  protected array $config     = [];
  public function setConfig(array $config)
  {
    $this->config = $config;
  }
}