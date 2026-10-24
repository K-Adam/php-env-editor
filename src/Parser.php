<?php

declare(strict_types=1);

namespace EnvEditor;

use EnvEditor\EnvFile\Block\Comment as CommentBlock;
use EnvEditor\EnvFile\Block\Unknown as UnknownBlock;
use EnvEditor\EnvFile\Block\Variable as VariableBlock;
use EnvEditor\EnvFile\Block\Variable\Key as VariableKey;
use EnvEditor\EnvFile\Block\Variable\Value as VariableValue;
use EnvEditor\Parser\EOLTypeDetector;
use RuntimeException;

class Parser
{

    public function __construct(private EOLTypeDetector $EOLTypeDetector = new EOLTypeDetector())
    {
    }

    public function parse(string $content): EnvFile
    {

        $file = new EnvFile();

        $file->EOL = $this->EOLTypeDetector->detect($content);
        $pregEOL = preg_quote($file->EOL, "/");

        $blockStart = "^";
        $blockEnd = "(?:$pregEOL|$)";
        $notEOLChar = "(?:(?!$pregEOL).)";
        $notEOLWhiteSpace = "(?:(?!$pregEOL)\s)";

        $comment = "#($notEOLChar*)";

        $notEOLNotSingleQuote = "(?:(?!$pregEOL)[^'])";
        $notDoubleQuote = "[^\"]";

        $variableKey = VariableKey::PATTERN;
        // Unquoted: can't start with "#"; possessive so "#" inside it (A=foo#bar) stays part of the value
        $notEOLNonSpace = "(?:(?!$pregEOL)\S)";
        $variableValue = "\"($notDoubleQuote*)\"|'($notEOLNotSingleQuote*)'|((?:(?!#)$notEOLNonSpace*+)?)";

        $variable = "($notEOLWhiteSpace*)($variableKey)($notEOLWhiteSpace*)=($notEOLWhiteSpace*)(?:$variableValue)($notEOLWhiteSpace*)(?:$comment)?";

        $matchedString = "";

        $parserContent = $content;
        while (strlen($parserContent) > 0) {
            $match = [];

            if (preg_match("/$blockStart(?:$comment)$blockEnd/D", $parserContent, $match)) {
                $block = new CommentBlock($match[1]);
            } elseif (preg_match("/$blockStart(?:$variable)$blockEnd/sD", $parserContent, $match, PREG_UNMATCHED_AS_NULL)) {

                [$vValue, $vQuote] = match (true) {
                    $match[5] !== null => [$match[5], '"'],
                    $match[6] !== null => [$match[6], "'"],
                    default => [$match[7], ""],
                };

                $bKey = new VariableKey($match[2]);
                $bKey->leftPad = $match[1];
                $bKey->rightPad = $match[3];

                $bValue = new VariableValue();
                $bValue->content = $vValue;
                $bValue->quote = $vQuote;
                $bValue->leftPad = $match[4];
                $bValue->rightPad = $match[8];

                $block = new VariableBlock($bKey, $bValue, $match[9]);
            } elseif (preg_match("/$blockStart($notEOLChar*)$blockEnd/sD", $parserContent, $match)) {
                $block = new UnknownBlock($match[1]);
            } else {
                throw new RuntimeException("Failed to parse .env content: " . preg_last_error_msg());
            }

            $file->blocks[] = $block;

            $matchedString = $match[0];
            $parserContent = substr($parserContent, strlen($matchedString));

        }

        if (str_ends_with($matchedString, $file->EOL) || strlen($content) == 0) {
            $file->blocks[] = new UnknownBlock();
        }

        return $file;

    }

}
