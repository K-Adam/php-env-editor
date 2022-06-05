<?php

namespace EnvEditor;

use EnvEditor\Composer\Visitor;

class Composer
{

    public function compose(EnvFile $file, ?string $overrideEOL = null): string
    {
        $visitor = new Visitor();

        $file->visitBlocks($visitor);

        $EOL = $overrideEOL ?? $file->EOL;
        return implode($EOL, $visitor->results);
    }

}
