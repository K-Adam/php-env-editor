<?php

namespace EnvEditor\EnvFile\Block;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Visitor;

class Comment extends Block {

  /** @var string */
  public $text="";

  function __construct($text = "") {
    $this->text = $text;
  }

  public function visit(Visitor $visitor) {
    $visitor->visitComment($this);
  }

}
