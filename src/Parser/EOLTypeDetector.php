<?php

namespace EnvEditor\Parser;

use EnvEditor\EnvFile\EOLType;

class EOLTypeDetector
{
    public function __construct(private string $default = EOLType::UNIX)
    {
    }

    public function detect(string $content): string
    {
        if (str_contains($content, EOLType::WINDOWS)) {
            return EOLType::WINDOWS;
        } elseif (str_contains($content, EOLType::UNIX)) {
            return EOLType::UNIX;
        }

        return $this->default;
    }
}
