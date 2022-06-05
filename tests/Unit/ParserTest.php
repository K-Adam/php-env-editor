<?php

namespace Tests\Unit;

use EnvEditor\EnvFile\EOLType;
use EnvEditor\Parser;
use Tests\TestCase;

class ParserTest extends TestCase
{
    
    public function testFileEOLType(): void
    {

        $parser = new Parser();

        $win = "#test\r\n#test";
        $unix = "#test\n#test";

        $this->assertEquals(EOLType::WINDOWS, $parser->parse($win)->EOL);
        $this->assertEquals(EOLType::UNIX, $parser->parse($unix)->EOL);

        $parser->EOL = EOLType::UNIX;

        $this->assertEquals(EOLType::UNIX, $parser->parse($win)->EOL);

    }

    /**
     * @depends testFileEOLType
     */
    public function testSimpleBlockCount(): void
    {
        $parser = new Parser();

        $win = "#test\r\n#test";
        $unix = "#test\n#test";

        $this->assertCount(2, $parser->parse($win)->blocks);
        $this->assertCount(2, $parser->parse($unix)->blocks);
    }

    /**
     * @depends testSimpleBlockCount
     */
    public function testComplexBlockCount(): void
    {
        $parser = new Parser();

        $multilineString = "#test\nX=VALUE\nY='foo bar'\nZ=\"foo\nbar\"";
        $this->assertCount(4, $parser->parse($multilineString)->blocks);

        $invalidValueString = "#test\nX=VALUE\nfoo bar";
        $this->assertCount(3, $parser->parse($invalidValueString)->blocks);
    }

    /**
     * @depends testSimpleBlockCount
     */
    public function testCommentBlockText(): void
    {
        $parser = new Parser();

        $blocks = $parser->parse("#test1\n#test2")->blocks;

        $this->assertEquals("test1", $blocks[0]->text);
        $this->assertEquals("test2", $blocks[1]->text);

    }

    /**
     * @depends testComplexBlockCount
     */
    public function testVariableBlockContent(): void
    {
        $parser = new Parser();

        $multilineString = "X=VALUE\nY='foo bar'\nZ=\"foo\nbar\"";
        $blocks = $parser->parse($multilineString)->blocks;

        $this->assertEquals("X", $blocks[0]->key);
        $this->assertEquals("VALUE", $blocks[0]->value);

        $this->assertEquals("Y", $blocks[1]->key);
        $this->assertEquals("foo bar", $blocks[1]->value);
        $this->assertEquals("'", $blocks[1]->value->quote);

        $this->assertEquals("Z", $blocks[2]->key);
        $this->assertEquals("foo\nbar", $blocks[2]->value);
        $this->assertEquals('"', $blocks[2]->value->quote);

    }

    /**
     * @depends testSimpleBlockCount
     */
    public function testVariableWhiteSpaces(): void
    {
        $parser = new Parser();

        $blocks = $parser->parse("X =foo\n\tY = 'bar'  ")->blocks;

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

    /**
     * @depends testSimpleBlockCount
     */
    public function testUnknown(): void
    {
        $parser = new Parser();
        $blocks = $parser->parse("???")->blocks;
        $this->assertEquals("???", $blocks[0]->content);
    }

    /**
     * @depends testSimpleBlockCount
     */
    public function testEmpty(): void
    {
        $parser = new Parser();
        $blocks = $parser->parse("#test\n\n#test")->blocks;
        $this->assertEquals("", $blocks[1]->content);
    }

    /**
     * @depends testEmpty
     */
    public function testEmptyLast(): void
    {
        $parser = new Parser();
        $blocks = $parser->parse("#test\n")->blocks;
        $this->assertEquals("", $blocks[1]->content);
    }

    public function testEmptyFile(): void
    {
        $parser = new Parser();

        $blocks = $parser->parse("")->blocks;

        $this->assertCount(1, $blocks);
        $this->assertEquals("", $blocks[0]->content);
    }
}
