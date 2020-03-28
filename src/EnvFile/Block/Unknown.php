<?php

namespace EnvEditor\EnvFile\Block;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Visitor;

class Unknown extends Block {

  /** @var string */
  public $content="";

  function __construct($content = "") {
    $this->content = $content;
  }

  public function visit(Visitor $visitor) {
    $visitor->visitUnknown($this);
  }

}
