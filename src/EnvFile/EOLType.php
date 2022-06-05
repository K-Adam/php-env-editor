<?php

namespace EnvEditor\EnvFile;

final class EOLType
{

    const WINDOWS = "\r\n";
    const UNIX = "\n";

    public static function detect(string $content, string $default = EOLType::UNIX): string
    {
        if (str_contains($content, EOLType::WINDOWS)) {
            return EOLType::WINDOWS;
        } elseif (str_contains($content, EOLType::UNIX)) {
            return EOLType::UNIX;
        }

        return $default;
    }
}
