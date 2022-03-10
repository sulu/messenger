<?php

declare(strict_types=1);

use Sulu\Messenger\Tests\Application\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

require \dirname(__DIR__) . '/bootstrap.php';

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

return new Application($kernel);
