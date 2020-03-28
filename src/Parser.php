<?php

namespace EnvEditor;

use EnvEditor\EnvFile\EOLType;
use EnvEditor\EnvFile\Block;
use EnvEditor\EnvFile\Block\Comment as CommentBlock;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;
use EnvEditor\EnvFile\Block\Unknown as UnknownBlock;
use EnvEditor\EnvFile\Block\Variable\Key as VariableKey;
use EnvEditor\EnvFile\Block\Variable\Value as VariableValue;

class Parser {

  /** @var string|null */
  public $EOL = null;

  public function parse(string $content): EnvFile {

    $file = new EnvFile();

    $file->EOL = $this->EOL ?? $this->detectEOLType($content);

    $blockStart = "^";
    $blockEnd = "(?:{$file->EOL}|$)";
    $notEOLChar = "(?:(?!{$file->EOL}).)";
    $notEOLWhiteSpace = "(?:(?!{$file->EOL})\s)";

    $comment = "#($notEOLChar*)";

    $notEOLNotSingleQuote = "(?:(?!{$file->EOL})[^'])";
    $notDoubleQuote = "[^\"]";

    $variableKey = "[a-zA-Z0-9_.]+";
    $variableValue = "\"$notDoubleQuote*\"|'$notEOLNotSingleQuote*'|\S*";

    $variable = "($notEOLWhiteSpace*)($variableKey)($notEOLWhiteSpace*)=($notEOLWhiteSpace*)($variableValue)($notEOLWhiteSpace*)";

    $matchedString = "";

    $parserContent = $content;
    while(strlen($parserContent) > 0) {
      $block = null;
      $match = [];

      if(preg_match("/$blockStart(?:$comment)$blockEnd/", $parserContent, $match)) {
        $commentText = $match[1];

        $block = new CommentBlock();
        $block->text = $commentText;
      } elseif(preg_match("/$blockStart(?:$variable)$blockEnd/s", $parserContent, $match)) {

        $vKey = $match[2];
        $vValue = $match[5];

        $vKeyPadLeft = $match[1];
        $vKeyPadRight = $match[3];
        $vValuePadLeft = $match[4];
        $vValuePadRight = $match[6];

        $vQuote = "";
        if(strlen($vValue) > 0) {
          if($vValue[0] == '"') {
            $vValue = substr($vValue, 1, strlen($vValue)-2);
            $vQuote = '"';
          } elseif($vValue[0] == "'") {
            $vValue = substr($vValue, 1, strlen($vValue)-2);
            $vQuote = "'";
          }
        }

        $bKey = new VariableKey();
        $bKey->content = $vKey;
        $bKey->leftPad = $vKeyPadLeft;
        $bKey->rightPad = $vKeyPadRight;

        $bValue = new VariableValue();
        $bValue->content = $vValue;
        $bValue->quote = $vQuote;
        $bValue->leftPad = $vValuePadLeft;
        $bValue->rightPad = $vValuePadRight;

        $block = new VariableBlock();
        $block->key = $bKey;
        $block->value = $bValue;
      } else {
        preg_match("/$blockStart($notEOLChar*)$blockEnd/s", $parserContent, $match);

        $uContent = $match[1];
        $block = new UnknownBlock($uContent);
      }

      $file->blocks[] = $block;

      $matchedString = $match[0];
      $parserContent = substr($parserContent, strlen($matchedString));

    }

    if((substr($matchedString, -strlen($file->EOL)) === $file->EOL) || strlen($content) == 0) {
      $file->blocks[] = new UnknownBlock();
    }

    return $file;

  }

  public function detectEOLType(string $content, string $default = EOLType::UNIX) {

    if (strpos($content, EOLType::WINDOWS) !== false) {
      return EOLType::WINDOWS;
    } elseif (strpos($content, EOLType::UNIX) !== false) {
      return EOLType::UNIX;
    }

    return $default;

  }

}
