<?php

namespace EnvEditor\EnvFile;

use EnvEditor\EnvFile\Block\Comment as CommentBlock;
use EnvEditor\EnvFile\Block\Unknown as UnknownBlock;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;

abstract class Visitor
{

    public function visitComment(CommentBlock $block): void
    {
    }

    public function visitVariable(VariableBlock $block): void
    {
    }

    public function visitUnknown(UnknownBlock $block): void
    {
    }

}
