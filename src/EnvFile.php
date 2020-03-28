<?php

namespace EnvEditor;

use EnvEditor\EnvFile\Visitor;
use EnvEditor\EnvFile\EOLType;

class EnvFile {

  /** @var string */
  public $EOL = "\n";

  /** @var \EnvEditor\EnvFile\Block[] */
  public $blocks = [];

  function __construct() {
    $this->EOL = EOLType::UNIX;
  }

  public function visitBlocks(Visitor $visitor) {
    foreach($this->blocks as $block) {
      $block->visit($visitor);
    }
  }

  // Utility methods

  public static function loadFrom(string $path): EnvFile {
    $parser = new Parser();
    $content = file_get_contents($path);

    return $parser->parse($content);
  }

  public function saveTo(string $path): void {
    $composer = new Composer();
    $content = $composer->compose($this);

    file_put_contents($path, $content);
  }

}
