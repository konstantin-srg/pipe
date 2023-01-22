<?php
namespace Pipe\Cache\Storage;

interface IStorage
{
  public function save(string $key, string $string): bool;
  public function load(string $key): string;
  public function has(string $key): bool;
  public function remove(string $key): bool;
}