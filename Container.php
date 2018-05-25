<?php
/**
 * IOC container
 *
 * @author luoage@msn.cn
 */
namespace Luoage;

use ReflectionClass;

class Container {

  /**
   * 实例化
   *
   * @static
   * @var
   */
  static public $instance;

  /**
   *
   */
  protected $bindings = [];

  /**
   *
   */
  protected $single = [];

  /**
   * 获取实例化
   *
   * @static
   *
   * @return new Container
   */
  static public function getInstance() {
    if (!self::$instance) {
      self::$instance = new static;
    }

    return self::$instance;
  }

  /**
   * 绑定
   *
   * @return void
   */
  public function bind($abstract, $concrete = null, $shared = false) {
    if (is_null($concrete)) {
      $concrete = $abstract;
    }

    if (!isset($this->bindings[$abstract])) {
      $this->bindings[$abstract] = compact('concrete', 'shared');
    }
  }

  /**
   * 单例
   *
   * @return void
   */
  public function singleton($abstract, $concrete = null) {
    $this->bind($abstract, $concrete, true);
  }

  /**
   * 获取对象
   *
   * @return new Class
   */
  public function make($abstract) {
    $this->bind($abstract);
    $concrete = $this->bindings[$abstract]['concrete'];

    if (isset($this->single[$concrete])) {
      return $this->single[$concrete];
    }

    $object = $this->resolveDependency($concrete);

    if ($this->bindings[$abstract]['shared']) {
      $this->single[$concrete] = $object;
    }

    return $object;
  }

  /**
   * 解决依赖
   *
   * @throw Error
   *
   * @return new Class
   */
  protected function resolveDependency($concrete) {
    $reflector = new ReflectionClass($concrete);

    if (!$reflector->isInstantiable()) {
      throw new Error($concrete . ' is not instantiable');
    }

    $constructor = $reflector->getConstructor();

    if (!$constructor) {
      return new $concrete;
    }

    $params = $constructor->getParameters();
    $args = [];

    foreach($params as $param) {
      $arg = null;

      if ($param->isDefaultValueAvailable()) {
        $arg = $param->getDefaultValue();
      }

      $class = $param->getClass();

      if ($class) {
        $arg = $this->make($class->name);
      }

      array_push($args, $arg);
    }

    return $reflector->newInstanceArgs($args);
  }

}
