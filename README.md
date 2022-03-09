<h1 align="center">Sulu Messenger</h1>

<p align="center">
    <a href="https://sulu.io/" target="_blank">
        <img width="30%" src="https://sulu.io/uploads/media/800x/00/230-Official%20Bundle%20Seal.svg?v=2-6&inline=1" alt="Official Sulu Bundle Badge">
    </a>
</p>

<p align="center">
    <a href="LICENSE" target="_blank">
        <img src="https://img.shields.io/github/license/sulu/messenger.svg" alt="GitHub license">
    </a>
    <a href="https://github.com/sulu/messenger/releases" target="_blank">
        <img src="https://img.shields.io/github/tag/sulu/messenger.svg" alt="GitHub tag (latest SemVer)">
    </a>
    <a href="https://github.com/sulu/messenger/actions" target="_blank">
        <img src="https://img.shields.io/github/workflow/status/sulu/messenger/Test%20application.svg?label=test-workflow" alt="Test workflow status">
    </a>
    <a href="https://github.com/sulu/sulu/releases" target="_blank">
        <img src="https://img.shields.io/badge/sulu%20compatibility-%3E=2.0-52b6ca.svg" alt="Sulu compatibility">
    </a>
</p>
<br/>

This library provides the stamps and middlewares which configures the sulu message bus.
It can be used independently in any symfony installation.

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Open a command console, enter your project directory and execute:

```bash
composer require sulu/messenger
```

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Sulu\Messenger\Infrastructure\Symfony\HttpKernel\SuluMessengerBundle::class => ['all' => true],
];
```

## Middlewares

### UnpackExceptionMiddleware

The `UnpackExceptionMiddleware` will unpack the `HandlerFailedException` which
is created by the Symfony [`HandleMessageMiddleware`](https://github.com/symfony/symfony/blob/c7dbcc954366f92f66360f3960a10dc1ef5f2584/src/Symfony/Component/Messenger/Middleware/HandleMessageMiddleware.php#L129).
This way we make sure that the real exception is thrown out by this message 
bus, and a controller can catch or convert it to a specific http status code.
This middleware is always activated in the sulu message bus.

### DoctrineFlushMiddleware

The `DoctrineFlushMiddleware` is a Middleware which let us flush the Doctrine
EntityManager by an opt-in flag via the `EnableFlushStamp`. It can be used this way:

```php
use Sulu\Messenger\Infrastructure\Symfony\Messenger\FlushMiddleware\EnableFlushStamp;

$this->handle(new Envelope(new YourMessage(), [new EnableFlushStamp()]));
```

This middleware is always activated in the sulu message bus.
