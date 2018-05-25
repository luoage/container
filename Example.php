<?php
namespace Luoage;

require_once('./Container.php');
require_once('./A.php');


class Example {
  public $e = 'e';
  public $b = 'b';

  public function __construct(A $a, Array $b = [], $m = 1, $x) {
    $this->a = $a->b;
    $this->b = $b;
    $this->m = $m;

    echo 'abc';
  }

  public function t1() {
    echo 'Example class';
  }

}


$container = Container::getInstance();

$container->bind(ABC::class, Example::class);
// print_r($container);

$example = $container->make(ABC::class);

$example = $container->make(ABC::class);

$example->t1();



?>
