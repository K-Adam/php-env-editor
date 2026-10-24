<?php

declare(strict_types=1);

namespace EnvEditor\EnvFile\Block;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Block\Variable\Key;
use EnvEditor\EnvFile\Block\Variable\Value;
use EnvEditor\EnvFile\Visitor;

class Variable extends Block
{

    public function __construct(
        public Key $key = new Key(),
        public Value $value = new Value(),
        public ?string $comment = null,
    ) {
    }

    public function visit(Visitor $visitor): void
    {
        $visitor->visitVariable($this);
    }

}
