# PHP .env editor

A `.env` file editor library for PHP.

## Usage example

```php
$envFile = \EnvEditor\EnvFile::loadFrom(__DIR__."/.env.example");

$envFile->setValue("exampleKey", "exampleValue");
$envFile->setValue("LOG_DIR", __DIR__."/logs");
// ...

$envFile->saveTo(__DIR__."/.env");
```
