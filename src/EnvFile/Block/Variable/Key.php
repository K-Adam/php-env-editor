<?php

declare(strict_types=1);

namespace EnvEditor\EnvFile\Block\Variable;

class Key
{

    use Padded;

    public const PATTERN = '[a-zA-Z0-9_.]+';

    public function __construct(public string $content = "")
    {
    }

    public function __toString(): string
    {
        return $this->content;
    }

}
