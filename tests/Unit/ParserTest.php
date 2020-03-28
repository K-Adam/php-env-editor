<?php

namespace Tests\Unit;

use Tests\TestCase;

use EnvEditor\Parser;
use EnvEditor\EnvFile\EOLType;

class ParserTest extends TestCase {

  public function testDetectEOLType() {
    $parser = new Parser();

    $win = "#test\r\n#test";
    $unix = "#test\n#test";
    $empty = "";

    $this->assertEquals(EOLType::WINDOWS, $parser->detectEOLType($win));
    $this->assertEquals(EOLType::UNIX, $parser->detectEOLType($unix));
    $this->assertEquals(EOLType::UNIX, $parser->detectEOLType($empty));
    $this->assertEquals(EOLType::UNIX, $parser->detectEOLType($empty, EOLType::UNIX));
    $this->assertEquals(EOLType::WINDOWS, $parser->detectEOLType($empty, EOLType::WINDOWS));

  }

  /**
   * @depends testDetectEOLType
   */
  public function testFileEOLType() {

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
  public function testSimpleBlockCount() {
    $parser = new Parser();

    $win = "#test\r\n#test";
    $unix = "#test\n#test";

    $this->assertEquals(2, count($parser->parse($win)->blocks));
    $this->assertEquals(2, count($parser->parse($unix)->blocks));
  }

  /**
   * @depends testSimpleBlockCount
   */
  public function testComplexBlockCount() {
    $parser = new Parser();

    $multilineString = "#test\nX=VALUE\nY='foo bar'\nZ=\"foo\nbar\"";
    $this->assertEquals(4, count($parser->parse($multilineString)->blocks));

    $invalidValueString = "#test\nX=VALUE\nfoo bar";
    $this->assertEquals(3, count($parser->parse($invalidValueString)->blocks));
  }

  /**
   * @depends testSimpleBlockCount
   */
  public function testCommentBlockText() {
    $parser = new Parser();

    $blocks = $parser->parse("#test1\n#test2")->blocks;

    $this->assertEquals("test1", $blocks[0]->text);
    $this->assertEquals("test2", $blocks[1]->text);

  }

  /**
   * @depends testComplexBlockCount
   */
  public function testVariableBlockContent() {
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
  public function testVariableWhiteSpaces() {
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
  public function testUnknown() {
    $parser = new Parser();
    $blocks = $parser->parse("???")->blocks;
    $this->assertEquals("???", $blocks[0]->content);
  }

  /**
   * @depends testSimpleBlockCount
   */
  public function testEmpty() {
    $parser = new Parser();
    $blocks = $parser->parse("#test\n\n#test")->blocks;
    $this->assertEquals("", $blocks[1]->content);
  }

  /**
   * @depends testEmpty
   */
  public function testEmptyLast() {
    $parser = new Parser();
    $blocks = $parser->parse("#test\n")->blocks;
    $this->assertEquals("", $blocks[1]->content);
  }

  public function testEmptyFile() {
    $parser = new Parser();

    $blocks = $parser->parse("")->blocks;

    $this->assertEquals(1, count($blocks));
    $this->assertEquals("", $blocks[0]->content);
  }
}
