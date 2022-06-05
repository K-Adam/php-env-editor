<?php

namespace Tests\Unit\Parser;

use EnvEditor\EnvFile\EOLType;
use EnvEditor\Parser\EOLTypeDetector;
use Tests\TestCase;

class EOLTypeDetectorTest extends TestCase
{

    private EOLTypeDetector $detector;

    private string $defaultEOL = '[DEFAULT_EOL]';

    protected function setUp(): void
    {
        parent::setUp();

        $this->detector = new EOLTypeDetector($this->defaultEOL);
    }


    public function testDetectUnix(): void
    {
        $unix = "#test\n#test";
        $this->assertEquals(EOLType::UNIX, $this->detector->detect($unix));
    }

    public function testDetectWindows(): void
    {
        $win = "#test\r\n#test";
        $this->assertEquals(EOLType::WINDOWS, $this->detector->detect($win));
    }

    public function testDetectDefault(): void
    {
        $empty = "";
        $this->assertEquals($this->defaultEOL, $this->detector->detect($empty));
    }
}
