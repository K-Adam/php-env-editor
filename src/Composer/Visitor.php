<?php

namespace EnvEditor\Composer;

use EnvEditor\EnvFile\Visitor as EnvVisitor;
use EnvEditor\EnvFile\Block\Comment as CommentBlock;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;
use EnvEditor\EnvFile\Block\Unknown as UnknownBlock;

class Visitor extends EnvVisitor {

  /** @var string[] */
  public $results = [];

  public function visitComment(CommentBlock $block){
    $this->addLine("#".$block->text);
  }

  public function visitVariable(VariableBlock $block){
    $keyStr = "{$block->key->leftPad}{$block->key->content}{$block->key->rightPad}";
    $valueStr = "{$block->value->leftPad}{$block->value->getContentWithQuotes()}{$block->value->rightPad}";

    $this->addLine("$keyStr=$valueStr");
  }

  public function visitUnknown(UnknownBlock $block){
    $this->addLine($block->content);
  }

  //

  private function addLine(string $line) {
    $this->results[] = $line;
  }

}
