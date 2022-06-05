<?php

namespace EnvEditor\EnvFile\Block;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Visitor;

class Unknown extends Block
{

    function __construct(public string $content = "")
    {
    }

    public function visit(Visitor $visitor): void
    {
        $visitor->visitUnknown($this);
    }

}
