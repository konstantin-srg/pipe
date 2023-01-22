<?php
namespace Pipe\Cache\Filter;

class FilterArray implements IFilter
{
  public function serialize($data): string
  {
    return serialize($data);
  }

  public function unserialize(string $data): array
  {
    return unserialize($data) ?: [];
  }
}