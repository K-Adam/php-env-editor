<?php

namespace Tests\Unit\EnvFile;

use EnvEditor\EnvFile\EOLType;
use Tests\TestCase;

class EOLTypeTest extends TestCase
{

    public function testDetectUnix(): void
    {
        $unix = "#test\n#test";
        $this->assertEquals(EOLType::UNIX, EOLType::detect($unix));
    }

    public function testDetectWindows(): void
    {
        $win = "#test\r\n#test";
        $this->assertEquals(EOLType::WINDOWS, EOLType::detect($win));
    }

    public function testDetectDefault(): void
    {
        $empty = "";
        $this->assertEquals(EOLType::UNIX, EOLType::detect($empty));
        $this->assertEquals(EOLType::UNIX, EOLType::detect($empty, EOLType::UNIX));
        $this->assertEquals(EOLType::WINDOWS, EOLType::detect($empty, EOLType::WINDOWS));
    }

}
