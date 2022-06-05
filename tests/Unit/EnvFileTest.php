<?php

namespace Tests\Unit;

use Tests\TestCase;

use EnvEditor\EnvFile;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;
use EnvEditor\EnvFile\Block\Variable\Key;
use EnvEditor\EnvFile\Block\Variable\Value;

class EnvFileTest extends TestCase {

  public function testFindVariable(): void {

    $file = new EnvFile();

    $v1 = new VariableBlock(new Key("A"), new Value("B"));
    $v2 = new VariableBlock(new Key("C"), new Value("D"));

    $file->blocks = [$v1, $v2];

    $this->assertEquals($v1, $file->findVariable("A"));
    $this->assertEquals($v2, $file->findVariable("C"));
    $this->assertEquals(null, $file->findVariable("X"));

  }

  /**
   * @depends testFindVariable
   */
  public function testPutVariable(): void {

    $file = new EnvFile();

    $v1 = new VariableBlock(new Key("A"), new Value("B"));
    $file->putVariable($v1);
    $this->assertEquals($v1, $file->findVariable("A"));

    $v2 = new VariableBlock(new Key("A"), new Value("C"));
    $file->putVariable($v2);
    $this->assertEquals($v2, $file->findVariable("A"));
    $this->assertCount(1, $file->blocks);

  }

  /**
   * @depends testFindVariable
   */
  public function testRemoveVariable(): void {

    $file = new EnvFile();

    $v1 = new VariableBlock(new Key("A"), new Value("B"));
    $v2 = new VariableBlock(new Key("C"), new Value("D"));

    $file->blocks = [$v1, $v2];

    $file->removeVariableKey("A");

    $this->assertEquals(null, $file->findVariable("A"));
    $this->assertEquals($v2, $file->findVariable("C"));
    $this->assertCount(1, $file->blocks);

  }

  /**
   * @depends testFindVariable
   */
  public function testGetValue(): void {

    $file = new EnvFile();

    $v1 = new VariableBlock(new Key("A"), new Value("B"));
    $v2 = new VariableBlock(new Key("C"), new Value("D"));

    $file->blocks = [$v1, $v2];

    $this->assertEquals("D", $file->getValue("C"));
    $this->assertEquals("", $file->getValue("X"));

  }

  /**
   * @depends testPutVariable
   */
  public function testSetValue(): void {

    $file = new EnvFile();

    $file->setValue("A", "B");
    $this->assertEquals("B", $file->getValue("A"));
    $file->setValue("A", "C");
    $this->assertEquals("C", $file->getValue("A"));

  }

  /**
   * @depends testSetValue
   */
  public function testSetValueQuoted(): void {

    $file = new EnvFile();

    $file->setValue("A", "foo bar");
    $v1 = $file->findVariable("A");
    $this->assertNotEquals("", $v1->value->quote);

    $file->setValue("B", "foo\nbar");
    $v2 = $file->findVariable("B");
    $this->assertEquals('"', $v2->value->quote);

  }
}
