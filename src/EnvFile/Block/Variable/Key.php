<?php

namespace EnvEditor\EnvFile\Block\Variable;

class Key
{

    use Padded;

    function __construct(public string $content = "")
    {
    }

    public function __toString()
    {
        return $this->content;
    }

}
