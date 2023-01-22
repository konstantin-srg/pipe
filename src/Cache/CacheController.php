<?php

namespace Pipe\Cache;

use Pipe\Cache\Filter;
use Pipe\Cache\Storage;
use Pipe\Exception\Exception;
use Pipe\StdLib\IConfigurable;
use Pipe\StdLib\TSingleton;

class CacheController implements IConfigurable
{
  use TSingleton;

  protected function __construct(){}

  protected array $controller;
  protected array $config     = [];
  protected string $cacheDir  = '';

  /* @var $storages Storage\IStorage[] */
  protected array $storages   = [];

  /* @var $filters Filter\IFilter[] */
  protected array $filters  = [];

  public function setConfig(array $config)
  {
    $this->config = $config;
  }

  protected function updateSubConfig($pluginsFld)
  {
    if(!isset($this->config[$pluginsFld])) return;
    $field = &$this->$pluginsFld;

    foreach ($this->config[$pluginsFld] as $pluginClass => $pluginConf) {
      if(isset($field[$pluginClass])) {
        $field[$pluginClass]->setConfig($pluginConf);
        unset($this->config[$pluginsFld][$pluginClass]);
      }
    }
  }

  public function init()
  {
    if(!isset($this->controller)) {
      try {
        $this->controller = $this->getFilter(filterClass: Filter\FilterArray::class)->unserialize(
          $this->getStorage()->load('cache-controller')
        );
      } catch (\Exception $e) {
        $this->controller = [];
      }
    }
  }

  private function getFilter($data = null, $filterClass = null): Filter\IFilter
  {
    if($data) {
      $filterClass = match (gettype($data)) {
        'NULL',
        'boolean',
        'integer',
        'string' => Filter\FilterString::class,
        'array'  => Filter\FilterArray::class,
        'object' => Filter\FilterObject::class,
        default  => throw new Exception('Unknown cache type'),
      };
    }

    if(!isset($this->filters[$filterClass])) {
      $this->filters[$filterClass] = new $filterClass();
      $this->updateSubConfig('filters');
    }

    return $this->filters[$filterClass];
  }

  public function getStorage($storageClass = null): Storage\IStorage
  {
    if(isset($this->storages[$storageClass])) {
      return $this->storages[$storageClass];
    }

    if(extension_loaded('apcu') && ini_get('apc.enabled')) {
      $storageClass = Storage\APCu::class;
    } else {
      $storageClass = Storage\Filesystem::class;
    }

    $this->storages[$storageClass] = $this->storages[$storageClass] ?? new $storageClass();

    if($this->storages[$storageClass] instanceof IConfigurable) {
      $this->updateSubConfig('storages');
    }

    return $this->storages[$storageClass];
  }

  /**
   * @param $key
   * @param $val
   * @return $this
   * @throws Exception
   */
  public function set($key, $data)
  {
    $this->init();
    $filter  = $this->getFilter(data: $data);
    $storage = $this->getStorage();

    if($storage->save($key, $filter->serialize($data))) {
      if(isset($this->controller[$key]) && array_diff($this->controller[$key], [
          'filterClass' => $filter::class,
          'storageClass' => $storage::class,
        ])) {
          $this->controller[$key] = [
              'filterClass'  => $filter::class,
              'storageClass' => $storage::class,
          ];
          $this->saveController();
      }
    }

    return $this;
  }

  protected function getFilterStorage($key)
  {
    if(!isset($this->controller[$key])) return false;

    return [
      'filter'  => $this->getFilter(filterClass: $this->controller[$key]['filterClass']),
      'storage' => $this->getStorage(storageClass: $this->controller[$key]['storageClass']),
    ];
  }

  public function get($key)
  {
    $this->init();

    if(!($fs = $this->getFilterStorage($key))) {
      return null;
    }

    return $fs['filter']->unserialize($fs['storage']->load($key));

    /*$controller = &$this->controller[$key];
    return $this->getFilter(filterClass: $controller['filterClass'])->unserialize(
      $this->getStorage(storageClass: $controller['storageClass'])->load($key)
    );*/
  }

  public function del($key)
  {
    if($fs = $this->getFilterStorage($key)) {
      $fs['storage']->remove($key);
    }
  }

  public function has($key)
  {
    return isset($this->controller[$key]);
  }

  protected function saveController()
  {
    $this->getStorage()->save('cache-controller',
      $this->getFilter(filterClass: Filter\FilterArray::class)->serialize($this->controller)
    );
  }
}