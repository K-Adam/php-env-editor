<?php

declare(strict_types=1);

namespace EnvEditor\Composer;

use EnvEditor\EnvFile\Block\Comment as CommentBlock;
use EnvEditor\EnvFile\Block\Unknown as UnknownBlock;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;
use EnvEditor\EnvFile\Visitor as EnvVisitor;

class Visitor extends EnvVisitor
{

    /** @var string[] */
    public array $results = [];

    public function visitComment(CommentBlock $block): void
    {
        $this->addLine("#" . $block->text);
    }

    private function addLine(string $line): void
    {
        $this->results[] = $line;
    }

    public function visitVariable(VariableBlock $block): void
    {
        $keyStr = "{$block->key->leftPad}{$block->key->content}{$block->key->rightPad}";
        $valueStr = "{$block->value->leftPad}{$block->value->getContentWithQuotes()}{$block->value->rightPad}";

        $commentStr = $block->comment === null ? "" : "#{$block->comment}";

        $this->addLine("$keyStr=$valueStr$commentStr");
    }

    public function visitUnknown(UnknownBlock $block): void
    {
        $this->addLine($block->content);
    }

}
