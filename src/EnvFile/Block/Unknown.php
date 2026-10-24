<?php

declare(strict_types=1);

namespace EnvEditor\EnvFile\Block;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Visitor;

class Unknown extends Block
{

    public function __construct(public string $content = "")
    {
    }

    public function visit(Visitor $visitor): void
    {
        $visitor->visitUnknown($this);
    }

}
