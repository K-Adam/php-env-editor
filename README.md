# PHP .env editor

A `.env` file editor library for PHP. Edits values while preserving comments, blank lines, padding, quotes and line endings.

## Installation

Requires PHP 8.2+.

```
composer require k-adam/env-editor
```

## Usage

```php
$envFile = \EnvEditor\EnvFile::loadFrom(__DIR__ . "/.env.example");

$envFile->getValue("APP_NAME");             // "" if missing
$envFile->setValue("APP_NAME", "My App");   // quotes the value if needed
$envFile->removeVariableKey("APP_DEBUG");

$envFile->saveTo(__DIR__ . "/.env");
```

Inline comments (`KEY=value # comment`) are kept. As in phpdotenv, `#` inside an unquoted value is literal (`KEY=foo#bar`).

`loadFrom` / `saveTo` throw `RuntimeException` on I/O errors; `setValue` throws `InvalidArgumentException`
for an invalid key or a value that can't be written back (escape sequences are not supported).
