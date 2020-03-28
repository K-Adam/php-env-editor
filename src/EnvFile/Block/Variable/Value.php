<?php

namespace EnvEditor\EnvFile\Block\Variable;

class Value {

  use Padded;

  /** @var string */
  public $content="";

  /** @var string */
  public $quote="";

  function __construct(string $content = "") {
    $this->setContent($content);
  }

  public function __toString() {
    return $this->content;
  }

  /** Adds quotes if necessary */
  public function setContent(string $content) {
    $this->content = $content;

    if(preg_match('/\s/s', $content)) {
      $this->quote = '"';
    }
  }

  public function getContentWithQuotes(): string {
    return "{$this->quote}{$this->content}{$this->quote}";
  }

}
