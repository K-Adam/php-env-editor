<?php

namespace EnvEditor;

use EnvEditor\Composer\Visitor;

class Composer
{

    public ?string $EOL = null;

    public function compose(EnvFile $file): string
    {
        $visitor = new Visitor();

        $file->visitBlocks($visitor);

        $EOL = $this->EOL ?? $file->EOL;
        return implode($EOL, $visitor->results);
    }

}
