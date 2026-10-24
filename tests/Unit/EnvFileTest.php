<?php

declare(strict_types=1);

namespace Tests\Unit;

use EnvEditor\EnvFile;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;
use EnvEditor\EnvFile\Block\Variable\Key;
use EnvEditor\EnvFile\Block\Variable\Value;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EnvFileTest extends TestCase
{

    public function testFindVariable(): void
    {

        $file = new EnvFile();

        $v1 = new VariableBlock(new Key("A"), new Value("B"));
        $v2 = new VariableBlock(new Key("C"), new Value("D"));

        $file->blocks = [$v1, $v2];

        $this->assertSame($v1, $file->findVariable("A"));
        $this->assertSame($v2, $file->findVariable("C"));
        $this->assertSame(null, $file->findVariable("X"));

    }

    public function testPutVariable(): void
    {

        $file = new EnvFile();

        $v1 = new VariableBlock(new Key("A"), new Value("B"));
        $file->putVariable($v1);
        $this->assertSame($v1, $file->findVariable("A"));

        $v2 = new VariableBlock(new Key("A"), new Value("C"));
        $file->putVariable($v2);
        $this->assertSame($v2, $file->findVariable("A"));
        $this->assertCount(1, $file->blocks);

    }

    public function testRemoveVariable(): void
    {

        $file = new EnvFile();

        $v1 = new VariableBlock(new Key("A"), new Value("B"));
        $v2 = new VariableBlock(new Key("C"), new Value("D"));

        $file->blocks = [$v1, $v2];

        $file->removeVariableKey("A");

        $this->assertSame(null, $file->findVariable("A"));
        $this->assertSame($v2, $file->findVariable("C"));
        $this->assertCount(1, $file->blocks);

    }

    public function testGetValue(): void
    {

        $file = new EnvFile();

        $v1 = new VariableBlock(new Key("A"), new Value("B"));
        $v2 = new VariableBlock(new Key("C"), new Value("D"));

        $file->blocks = [$v1, $v2];

        $this->assertEquals("D", $file->getValue("C"));
        $this->assertEquals("", $file->getValue("X"));

    }

    public function testSetValue(): void
    {

        $file = new EnvFile();

        $file->setValue("A", "B");
        $this->assertEquals("B", $file->getValue("A"));
        $file->setValue("A", "C");
        $this->assertEquals("C", $file->getValue("A"));

    }

    public function testSetValueQuoted(): void
    {

        $file = new EnvFile();

        $file->setValue("A", "foo bar");
        $v1 = $file->findVariable("A");
        $this->assertEquals('"', $v1->value->quote);

        $file->setValue("B", "foo\nbar");
        $v2 = $file->findVariable("B");
        $this->assertEquals('"', $v2->value->quote);

        $file->setValue("C", 'say "hi"');
        $this->assertEquals("'", $file->findVariable("C")->value->quote);

    }

    public function testSetValueUnquotable(): void
    {
        $file = new EnvFile();
        $file->setValue("A", "old");

        try {
            $file->setValue("A", "\"it's\" broken");
            $this->fail("Expected InvalidArgumentException");
        } catch (InvalidArgumentException) {
            $this->assertEquals("old", $file->getValue("A"));
        }
    }

    public function testSetValueInvalidKey(): void
    {
        $file = new EnvFile();

        foreach (["", "BAD KEY", "A=B", "A\n"] as $key) {
            try {
                $file->setValue($key, "x");
                $this->fail("Expected InvalidArgumentException for '$key'");
            } catch (InvalidArgumentException) {
            }
        }

        $this->assertCount(0, $file->blocks);
    }

    public function testKeysComparedStrictly(): void
    {
        $file = new EnvFile();

        $file->setValue("10", "a");
        $file->setValue("1e1", "b");
        $file->putVariable(new VariableBlock(new Key("010"), new Value("c")));

        $this->assertEquals("a", $file->getValue("10"));
        $this->assertEquals("b", $file->getValue("1e1"));
        $this->assertEquals("c", $file->getValue("010"));
        $this->assertCount(3, $file->blocks);
    }
}
