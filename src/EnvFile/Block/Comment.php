<?php

declare(strict_types=1);

namespace EnvEditor\EnvFile\Block;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Visitor;

class Comment extends Block
{

    public function __construct(public string $text = "")
    {
    }

    public function visit(Visitor $visitor): void
    {
        $visitor->visitComment($this);
    }

}
