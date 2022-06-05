<?php

namespace Tests\Unit\Composer;

use EnvEditor\Composer\Visitor;
use EnvEditor\EnvFile\Block\Comment as CommentBlock;
use EnvEditor\EnvFile\Block\Unknown as UnknownBlock;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;
use EnvEditor\EnvFile\Block\Variable\Key as VariableKey;
use EnvEditor\EnvFile\Block\Variable\Value as VariableValue;
use Tests\TestCase;

class VisitorTest extends TestCase
{

    public function testComment(): void
    {
        $visitor = new Visitor();

        $comment = new CommentBlock();
        $comment->text = "foo";
        $visitor->visitComment($comment);

        $this->assertEquals(["#foo"], $visitor->results);
    }

    public function testVariable(): void
    {
        $visitor = new Visitor();

        $variable = new VariableBlock();
        $variable->key = new VariableKey("X");
        $variable->value = new VariableValue("Y");
        $visitor->visitVariable($variable);

        $this->assertEquals(["X=Y"], $visitor->results);
    }

    /**
     * @depends testVariable
     */
    public function testQuotedVariable(): void
    {
        $visitor = new Visitor();

        $variable = new VariableBlock();
        $variable->key = new VariableKey("X");
        $variable->value = new VariableValue("Y");
        $variable->value->quote = "'";
        $visitor->visitVariable($variable);

        $this->assertEquals(["X='Y'"], $visitor->results);
    }

    /**
     * @depends testVariable
     */
    public function testPaddedVariable(): void
    {
        $visitor = new Visitor();

        $variable = new VariableBlock();

        $variable->key = new VariableKey("X");
        $variable->value = new VariableValue("Y");

        $variable->key->leftPad = " ";
        $variable->key->rightPad = "  ";

        $variable->value->leftPad = "\t";
        $variable->value->rightPad = " ";

        $visitor->visitVariable($variable);

        $this->assertEquals([" X  =\tY "], $visitor->results);
    }

    public function testUnknown()
    {
        $visitor = new Visitor();

        $unknown = new UnknownBlock();
        $unknown->content = "???";
        $visitor->visitUnknown($unknown);

        $this->assertEquals(["???"], $visitor->results);
    }

}
