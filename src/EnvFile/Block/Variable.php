<?php

namespace EnvEditor\EnvFile\Block;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Visitor;
use EnvEditor\EnvFile\Block\Variable\Key;
use EnvEditor\EnvFile\Block\Variable\Value;

class Variable extends Block {

  /** @var Key */
  public $key;

  /** @var Value */
  public $value;

  function __construct(Key $key = null, Value $value = null) {
    $this->key = $key ?? new Key();
    $this->value = $value ?? new Value();
  }

  public function visit(Visitor $visitor) {
    $visitor->visitVariable($this);
  }

}
