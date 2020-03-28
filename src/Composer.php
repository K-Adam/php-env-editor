<?php

namespace EnvEditor;

use EnvEditor\Composer\Visitor;

class Composer {

  /** @var string|null */
  public $EOL = null;

  public function compose(EnvFile $file): string {
    $visitor = new Visitor();

    $file->visitBlocks($visitor);

    $EOL = $this->EOL ?? $file->EOL;
    $result = implode($EOL, $visitor->results);
    return $result;
  }

}
