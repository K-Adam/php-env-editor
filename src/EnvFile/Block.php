<?php

declare(strict_types=1);

namespace EnvEditor\EnvFile;

abstract class Block
{

    abstract public function visit(Visitor $visitor): void;

}
