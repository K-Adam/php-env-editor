<?php

namespace EnvEditor\EnvFile;

use EnvEditor\EnvFile\Block\Comment as CommentBlock;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;
use EnvEditor\EnvFile\Block\Unknown as UnknownBlock;

abstract class Visitor {

  public function visitComment(CommentBlock $block){}
  public function visitVariable(VariableBlock $block){}
  public function visitUnknown(UnknownBlock $block){}

}
