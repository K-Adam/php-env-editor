<?php

namespace Tests\Unit\EnvFile;

use Tests\TestCase;

use EnvEditor\EnvFile;
use EnvEditor\EnvFile\Visitor;
use EnvEditor\EnvFile\Block\Comment as CommentBlock;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;

class VisitorTest extends TestCase {

  private EnvFile $file;

  protected function setUp(): void {
    $this->file = new EnvFile();

    $b1 = new CommentBlock();
    $b2 = new VariableBlock();
    $b3 = new VariableBlock();

    $this->file->blocks = [$b1, $b2, $b3];
  }

  public function testAllVisited(): void {
    $visitor = new class extends Visitor {
      public array $visited = [];

      public function visitComment(CommentBlock $block): void {$this->visited[] = $block;}
      public function visitVariable(VariableBlock $block): void {$this->visited[] = $block;}
    };

    $this->file->visitBlocks($visitor);
    $this->assertEquals($this->file->blocks, $visitor->visited);
  }

}
