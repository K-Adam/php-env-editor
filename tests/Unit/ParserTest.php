<?php

declare(strict_types=1);

namespace Tests\Unit;

use EnvEditor\EnvFile\Block\Unknown as UnknownBlock;
use EnvEditor\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{

    private Parser $parser;

    private string $newLine = "[TEST_EOL]";

    protected function setUp(): void
    {
        parent::setUp();

        $detector = $this->createMock(Parser\EOLTypeDetector::class);
        $detector->method('detect')->willReturn($this->newLine);
        $this->parser = new Parser($detector);
    }

    public function testSimpleBlockCount(): void
    {
        $content = "#test{$this->newLine}#test";

        $this->assertCount(2, $this->parser->parse($content)->blocks);
    }

    public function testComplexBlockCount(): void
    {
        $multilineString = "#test{$this->newLine}X=VALUE{$this->newLine}Y='foo bar'{$this->newLine}Z=\"foo{$this->newLine}bar\"";
        $this->assertCount(4, $this->parser->parse($multilineString)->blocks);

        $invalidValueString = "#test{$this->newLine}X=VALUE{$this->newLine}foo bar";
        $this->assertCount(3, $this->parser->parse($invalidValueString)->blocks);
    }

    public function testCommentBlockText(): void
    {
        $blocks = $this->parser->parse("#test1{$this->newLine}#test2")->blocks;

        $this->assertEquals("test1", $blocks[0]->text);
        $this->assertEquals("test2", $blocks[1]->text);

    }

    public function testVariableBlockContent(): void
    {
        $multilineString = "X=VALUE{$this->newLine}Y='foo bar'{$this->newLine}Z=\"foo{$this->newLine}bar\"";
        $blocks = $this->parser->parse($multilineString)->blocks;

        $this->assertEquals("X", $blocks[0]->key);
        $this->assertEquals("VALUE", $blocks[0]->value);

        $this->assertEquals("Y", $blocks[1]->key);
        $this->assertEquals("foo bar", $blocks[1]->value);
        $this->assertEquals("'", $blocks[1]->value->quote);

        $this->assertEquals("Z", $blocks[2]->key);
        $this->assertEquals("foo{$this->newLine}bar", $blocks[2]->value);
        $this->assertEquals('"', $blocks[2]->value->quote);

    }

    public function testVariableWhiteSpaces(): void
    {
        $blocks = $this->parser->parse("X =foo{$this->newLine}\tY = 'bar'  ")->blocks;

        $this->assertEquals("X", $blocks[0]->key);
        $this->assertEquals("foo", $blocks[0]->value);

        $this->assertEquals("Y", $blocks[1]->key);
        $this->assertEquals("bar", $blocks[1]->value);

        $this->assertEquals("", $blocks[0]->key->leftPad);
        $this->assertEquals(" ", $blocks[0]->key->rightPad);
        $this->assertEquals("", $blocks[0]->value->leftPad);
        $this->assertEquals("", $blocks[0]->value->rightPad);

        $this->assertEquals("\t", $blocks[1]->key->leftPad);
        $this->assertEquals(" ", $blocks[1]->key->rightPad);
        $this->assertEquals(" ", $blocks[1]->value->leftPad);
        $this->assertEquals("  ", $blocks[1]->value->rightPad);
    }

    public function testInlineComment(): void
    {
        $blocks = $this->parser->parse("A=foo # note")->blocks;
        $this->assertEquals("foo", $blocks[0]->value);
        $this->assertEquals(" ", $blocks[0]->value->rightPad);
        $this->assertSame(" note", $blocks[0]->comment);

        $blocks = $this->parser->parse("A=\"x y\"#c")->blocks;
        $this->assertEquals("x y", $blocks[0]->value);
        $this->assertSame("c", $blocks[0]->comment);

        $blocks = $this->parser->parse("A=#c")->blocks;
        $this->assertEquals("", $blocks[0]->value);
        $this->assertSame("c", $blocks[0]->comment);
    }

    public function testHashInsideUnquotedValue(): void
    {
        $blocks = $this->parser->parse("A=foo#bar")->blocks;
        $this->assertEquals("foo#bar", $blocks[0]->value);
        $this->assertNull($blocks[0]->comment);

        $blocks = $this->parser->parse("A=foo# bar")->blocks;
        $this->assertInstanceOf(UnknownBlock::class, $blocks[0]);
    }

    public function testUnknown(): void
    {
        $blocks = $this->parser->parse("???")->blocks;
        $this->assertEquals("???", $blocks[0]->content);
    }

    public function testEmpty(): void
    {
        $blocks = $this->parser->parse("#test{$this->newLine}{$this->newLine}#test")->blocks;
        $this->assertEquals("", $blocks[1]->content);
    }

    public function testEmptyLast(): void
    {
        $blocks = $this->parser->parse("#test{$this->newLine}")->blocks;
        $this->assertEquals("", $blocks[1]->content);
    }

    public function testEmptyFile(): void
    {
        $blocks = $this->parser->parse("")->blocks;

        $this->assertCount(1, $blocks);
        $this->assertEquals("", $blocks[0]->content);
    }
}
