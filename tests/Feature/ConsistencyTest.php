<?php

declare(strict_types=1);

namespace Tests\Feature;

use EnvEditor\Composer;
use EnvEditor\EnvFile;
use EnvEditor\Parser;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ConsistencyTest extends TestCase
{

    private Parser $parser;
    private Composer $composer;

    protected function setUp(): void
    {
        $this->parser = new Parser();
        $this->composer = new Composer();
    }

    public function testKeepEOL(): void
    {
        $contentUnix = "#test\n#test";
        $contentWindows = "#test\r\n#test";

        $this->assertEquals($contentUnix, $this->composer->compose($this->parser->parse($contentUnix)));
        $this->assertEquals($contentWindows, $this->composer->compose($this->parser->parse($contentWindows)));
    }

    public function testComplexFile(): void
    {
        $content = file_get_contents(__DIR__ . "/Asset/example.env");

        $this->assertEquals($content, $this->composer->compose($this->parser->parse($content)));
    }

    public function testRoundTripUnusualQuotes(): void
    {
        $contents = [
            "A=\"foo", "A='foo", "A=\"foo\"bar\"", "A=\"\"", "A=''", "A=1\r\n#a\n", "A=1\r\nB=2\n",
            "A=foo # note", "A=\"x y\"#c ", "A= # c", "A=#c", "A=#", "A=foo#bar", "A=foo# bar", "A=1 #a\r\nB=2 #b\r\n",
        ];

        foreach ($contents as $content) {
            $this->assertEquals($content, $this->composer->compose($this->parser->parse($content)));
        }
    }

    public function testLoadSave(): void
    {
        $contentPath = __DIR__ . "/Asset/example.env";
        $outputPath = tempnam(sys_get_temp_dir(), "env");

        try {
            EnvFile::loadFrom($contentPath)->saveTo($outputPath);
            $this->assertEquals(file_get_contents($contentPath), file_get_contents($outputPath));
        } finally {
            unlink($outputPath);
        }
    }

    public function testLoadMissingFile(): void
    {
        $this->expectException(RuntimeException::class);
        EnvFile::loadFrom(__DIR__ . "/Asset/missing.env");
    }

    public function testSetValueKeepsTrailingNewline(): void
    {
        $file = $this->parser->parse("A=1\n");
        $file->setValue("B", "2");
        $this->assertEquals("A=1\nB=2\n", $this->composer->compose($file));

        $file = $this->parser->parse("");
        $file->setValue("A", "1");
        $this->assertEquals("A=1\n", $this->composer->compose($file));

        $file = $this->parser->parse("A=1");
        $file->setValue("B", "2");
        $this->assertEquals("A=1\nB=2", $this->composer->compose($file));
    }

    public function testSetValueRoundTrip(): void
    {
        $values = ["plain", "", "foo bar", "say \"hi\" now", "it's ok", "multi\nline", "#hash", "\"quoted", "a=b"];

        foreach ($values as $value) {
            $file = new EnvFile();
            $file->setValue("A", $value);
            $parsed = $this->parser->parse($this->composer->compose($file));

            $this->assertSame($value, $parsed->getValue("A"), $value);
        }
    }

    public function testSetValueKeepsInlineComment(): void
    {
        $file = $this->parser->parse("A=1 # keep\n");
        $this->assertEquals("1", $file->getValue("A"));

        $file->setValue("A", "2");
        $this->assertEquals("A=2 # keep\n", $this->composer->compose($file));
    }

    public function testValueWithOtherLineBreakKeepsFileEOL(): void
    {
        $file = $this->parser->parse("A=1\nC=3\n");
        $file->setValue("B", "x\r\ny");
        $parsed = $this->parser->parse($this->composer->compose($file));

        $this->assertEquals("1", $parsed->getValue("A"));
        $this->assertEquals("x\r\ny", $parsed->getValue("B"));
        $this->assertEquals("3", $parsed->getValue("C"));
    }

}
