<?php

namespace EnvEditor\EnvFile\Block;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Visitor;
use EnvEditor\EnvFile\Block\Variable\Key;
use EnvEditor\EnvFile\Block\Variable\Value;

class Variable extends Block {

  public Key $key;
  public Value $value;

  // TODO: Use property promotion in 8.1
  function __construct(Key $key = null, Value $value = null) {
    $this->key = $key ?? new Key();
    $this->value = $value ?? new Value();
  }

  public function visit(Visitor $visitor): void {
    $visitor->visitVariable($this);
  }

}
