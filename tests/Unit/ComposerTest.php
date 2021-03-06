<?php

namespace Tests\Unit;

use Tests\TestCase;

use EnvEditor\Composer;
use EnvEditor\EnvFile;
use EnvEditor\EnvFile\EOLType;
use EnvEditor\EnvFile\Block\Comment as CommentBlock;

class ComposerTest extends TestCase {

  public function testCompose() {
    $composer = new Composer();
    $file = new EnvFile();

    $file->blocks[] = new CommentBlock("test1");
    $file->blocks[] = new CommentBlock("test2");

    $result = $composer->compose($file);
    $this->assertEquals("#test1\n#test2", $result);
  }

  public function testEOL() {
    $composer = new Composer();
    $file = new EnvFile();

    $file->blocks[] = new CommentBlock("test1");
    $file->blocks[] = new CommentBlock("test2");

    $file->EOL = EOLType::UNIX;
    $this->assertEquals("#test1\n#test2", $composer->compose($file));

    $file->EOL = EOLType::WINDOWS;
    $this->assertEquals("#test1\r\n#test2", $composer->compose($file));

    $composer->EOL = EOLType::UNIX;
    $this->assertEquals("#test1\n#test2", $composer->compose($file));
  }

}
