<?php

namespace EnvEditor;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;
use EnvEditor\EnvFile\EOLType;
use EnvEditor\EnvFile\Visitor;

class EnvFile
{

    public string $EOL = "\n";

    /** @var Block[] */
    public array $blocks = [];

    function __construct()
    {
        $this->EOL = EOLType::UNIX;
    }

    public static function loadFrom(string $path): EnvFile
    {
        $parser = new Parser();
        $content = file_get_contents($path);

        return $parser->parse($content);
    }

    public function visitBlocks(Visitor $visitor): void
    {
        foreach ($this->blocks as $block) {
            $block->visit($visitor);
        }
    }

    public function putVariable(VariableBlock $variable): void
    {
        foreach ($this->blocks as $i => $block) {

            if (!$block instanceof VariableBlock) continue;

            if ($block->key->content == $variable->key->content) {
                $this->blocks[$i] = $variable;
                return;
            }
        }

        $this->blocks[] = $variable;
    }

    public function removeVariableKey(string $key): ?VariableBlock
    {
        $variable = $this->findVariable($key);
        if (!$variable) return null;

        $index = array_search($variable, $this->blocks);
        array_splice($this->blocks, $index, 1);
        return $variable;
    }

    public function findVariable(string $key): ?VariableBlock
    {
        foreach ($this->getVariableBlocks() as $block) {
            if ($block->key == $key) {
                return $block;
            }
        }

        return null;
    }

    /**
     * @return VariableBlock[]
     */
    public function getVariableBlocks(): array
    {
        return array_values(array_filter($this->blocks, function ($block) {
            return $block instanceof VariableBlock;
        }));
    }

    public function getValue(string $key): string
    {
        $variable = $this->findVariable($key);

        return $variable ? $variable->value->content : "";
    }

    // Utility methods

    public function setValue(string $key, string $content): void
    {
        $variable = $this->findVariable($key);
        if (!$variable) {
            $variable = new VariableBlock();
            $variable->key->content = $key;

            $this->blocks[] = $variable;
        }

        $variable->value->setContent($content);
    }

    public function saveTo(string $path): void
    {
        $composer = new Composer();
        $content = $composer->compose($this);

        file_put_contents($path, $content);
    }

    // Private methods


}
