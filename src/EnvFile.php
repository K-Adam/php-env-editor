<?php

declare(strict_types=1);

namespace EnvEditor;

use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Block\Unknown as UnknownBlock;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;
use EnvEditor\EnvFile\Block\Variable\Key;
use EnvEditor\EnvFile\Block\Variable\Value;
use EnvEditor\EnvFile\EOLType;
use EnvEditor\EnvFile\Visitor;
use InvalidArgumentException;
use RuntimeException;

class EnvFile
{

    /** @var Block[] */
    public array $blocks = [];

    public function __construct(public string $EOL = EOLType::UNIX)
    {
    }

    public static function loadFrom(string $path): EnvFile
    {
        $content = @file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException("Unable to read file: $path");
        }

        return (new Parser())->parse($content);
    }

    public function visitBlocks(Visitor $visitor): void
    {
        foreach ($this->blocks as $block) {
            $block->visit($visitor);
        }
    }

    public function putVariable(VariableBlock $variable): void
    {
        $existing = $this->findVariable($variable->key->content);

        if ($existing) {
            $this->blocks[array_search($existing, $this->blocks, true)] = $variable;
        } else {
            $this->appendBlock($variable);
        }
    }

    public function removeVariableKey(string $key): ?VariableBlock
    {
        $variable = $this->findVariable($key);
        if (!$variable) return null;

        $index = array_search($variable, $this->blocks, true);
        array_splice($this->blocks, $index, 1);
        return $variable;
    }

    public function findVariable(string $key): ?VariableBlock
    {
        foreach ($this->getVariableBlocks() as $block) {
            if ($block->key->content === $key) {
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
        return array_values(array_filter($this->blocks, fn($block) => $block instanceof VariableBlock));
    }

    public function getValue(string $key): string
    {
        $variable = $this->findVariable($key);

        return $variable ? $variable->value->content : "";
    }

    // Utility methods

    public function setValue(string $key, string $content): void
    {
        if (!preg_match('/^' . Key::PATTERN . '$/D', $key)) {
            throw new InvalidArgumentException("Invalid key: '$key'");
        }

        $variable = $this->findVariable($key);
        if ($variable) {
            $variable->value->setContent($content);
            return;
        }

        $this->appendBlock(new VariableBlock(new Key($key), new Value($content)));
    }

    public function saveTo(string $path): void
    {
        $content = (new Composer())->compose($this);

        if (@file_put_contents($path, $content, LOCK_EX) === false) {
            throw new RuntimeException("Unable to write file: $path");
        }
    }

    /** Keeps a trailing EOL at the end of the file */
    private function appendBlock(Block $block): void
    {
        $last = end($this->blocks);

        if ($last instanceof UnknownBlock && $last->content === "") {
            array_splice($this->blocks, -1, 0, [$block]);
        } else {
            $this->blocks[] = $block;
        }
    }

}
