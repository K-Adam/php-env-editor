<?php

declare(strict_types=1);

namespace EnvEditor\EnvFile\Block\Variable;

use InvalidArgumentException;

class Value
{

    use Padded;

    public string $content = "";

    public string $quote = "";

    public function __construct(string $content = "")
    {
        $this->setContent($content);
    }

    /** Picks a quote style that parses back to the same content, preferring the current one */
    public function setContent(string $content): void
    {
        foreach ([$this->quote, '', '"', "'"] as $quote) {
            $valid = match ($quote) {
                '' => !preg_match('/\s|^["\'#]/', $content),
                '"' => !str_contains($content, '"'),
                "'" => !preg_match('/[\'\r\n]/', $content),
                default => false,
            };

            if ($valid) {
                $this->content = $content;
                $this->quote = $quote;
                return;
            }
        }

        throw new InvalidArgumentException("Value cannot be quoted: it needs quotes but contains \" and also ' or a line break");
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function getContentWithQuotes(): string
    {
        return "{$this->quote}{$this->content}{$this->quote}";
    }

}
