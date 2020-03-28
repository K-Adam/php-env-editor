<?php

namespace EnvEditor\EnvFile\Block\Variable;

class Value {

  use Padded;

  /** @var string */
  public $content="";

  /** @var string */
  public $quote="";

  function __construct($content = "") {
    $this->content = $content;
  }

  public function __toString() {
    return $this->content;
  }

  public function getContentWithQuotes(): string {
    return "{$this->quote}{$this->content}{$this->quote}";
  }

}
