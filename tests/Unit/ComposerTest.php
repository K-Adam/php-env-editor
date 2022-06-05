<?php

namespace Tests\Unit;

use EnvEditor\Composer;
use EnvEditor\EnvFile;
use EnvEditor\EnvFile\Block\Comment as CommentBlock;
use EnvEditor\EnvFile\EOLType;
use Tests\TestCase;

class ComposerTest extends TestCase
{

    private Composer $composer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->composer = new Composer();
    }

    public function testCompose(): void
    {
        $file = new EnvFile();

        $file->blocks[] = new CommentBlock("test1");
        $file->blocks[] = new CommentBlock("test2");

        $result = $this->composer->compose($file);
        $this->assertEquals("#test1\n#test2", $result);
    }

    public function testEOL(): void
    {
        $file = new EnvFile();

        $file->blocks[] = new CommentBlock("test1");
        $file->blocks[] = new CommentBlock("test2");

        $this->assertEquals("#test1\n#test2", $this->composer->compose($file, EOLType::UNIX));
        $this->assertEquals("#test1\r\n#test2", $this->composer->compose($file, EOLType::WINDOWS));
        $this->assertEquals("#test1\n#test2", $this->composer->compose($file, EOLType::UNIX));
    }

}
