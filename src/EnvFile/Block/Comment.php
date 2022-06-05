<?php

namespace EnvEditor\EnvFile\Block;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Visitor;

class Comment extends Block
{

    function __construct(public string $text = "")
    {
    }

    public function visit(Visitor $visitor): void
    {
        $visitor->visitComment($this);
    }

}
