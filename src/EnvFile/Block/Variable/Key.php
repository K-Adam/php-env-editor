<?php

namespace EnvEditor\EnvFile\Block\Variable;

class Key {

  use Padded;

  /** @var string */
  public $content="";

  function __construct($content = "") {
    $this->content = $content;
  }

  public function __toString() {
    return $this->content;
  }

}
