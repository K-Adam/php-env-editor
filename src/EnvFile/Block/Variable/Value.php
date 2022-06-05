<?php

namespace EnvEditor\EnvFile\Block\Variable;

class Value
{

    use Padded;

    public string $content = "";

    public string $quote = "";

    function __construct(string $content = "")
    {
        $this->setContent($content);
    }

    /** Adds quotes if necessary */
    public function setContent(string $content): void
    {
        $this->content = $content;

        if (preg_match('/\s/s', $content)) {
            $this->quote = '"';
        }
    }

    public function __toString()
    {
        return $this->content;
    }

    public function getContentWithQuotes(): string
    {
        return "{$this->quote}{$this->content}{$this->quote}";
    }

}
