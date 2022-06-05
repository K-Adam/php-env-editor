<?php

namespace Tests\Feature;

use EnvEditor\Composer;
use EnvEditor\EnvFile;
use EnvEditor\Parser;
use EnvEditor\Parser\EOLTypeDetector;
use Tests\TestCase;

class ConsistencyTest extends TestCase
{

    public function testKeepEOL()
    {
        $parser = new Parser(new EOLTypeDetector());
        $composer = new Composer();

        $contentUnix = "#test\n#test";
        $contentWindows = "#test\r\n#test";

        $this->assertEquals($contentUnix, $composer->compose($parser->parse($contentUnix)));
        $this->assertEquals($contentWindows, $composer->compose($parser->parse($contentWindows)));
    }

    /**
     * @depends testKeepEOL
     */
    public function testComplexFile()
    {

        $parser = new Parser(new EOLTypeDetector());
        $composer = new Composer();

        $content = file_get_contents(__DIR__ . "/Asset/example.env");

        $this->assertEquals($content, $composer->compose($parser->parse($content)));

    }

    /**
     * @depends testComplexFile
     */
    public function testLoadSave()
    {

        $contentPath = __DIR__ . "/Asset/example.env";
        $content = file_get_contents($contentPath);

        $tmpPath = dirname(__DIR__, 2) . "/.tmp";

        if (!is_dir($tmpPath)) {
            mkdir($tmpPath);
        }

        $outputPath = "$tmpPath/example.env";
        if (file_exists($outputPath)) {
            unlink($outputPath);
        }

        EnvFile::loadFrom($contentPath)->saveTo($outputPath);

        $output = file_get_contents($outputPath);

        $this->assertEquals($content, $output);

        unlink($outputPath);
        rmdir($tmpPath);

    }

}
