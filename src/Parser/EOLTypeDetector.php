<?php

declare(strict_types=1);

namespace EnvEditor\Parser;

use EnvEditor\EnvFile\EOLType;

class EOLTypeDetector
{
    public function __construct(private string $default = EOLType::UNIX)
    {
    }

    public function detect(string $content): string
    {
        // Ignore line breaks inside double-quoted values; majority wins
        // ponytail: naive pairing, an unmatched " in a comment can skew it
        $content = preg_replace('/"[^"]*"/', '', $content) ?? $content;

        $windows = substr_count($content, EOLType::WINDOWS);
        $unix = substr_count($content, EOLType::UNIX) - $windows;

        if ($windows === 0 && $unix === 0) {
            return $this->default;
        }

        return $windows > $unix ? EOLType::WINDOWS : EOLType::UNIX;
    }
}
