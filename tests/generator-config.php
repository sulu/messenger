<?php

declare(strict_types=1);

use Schranz\TestGenerator\Domain\Model\Config;

$config = new Config();
$config->hooks[] = 'vendor/bin/rector process %s';
$config->hooks[] = 'vendor/bin/php-cs-fixer fix %s';

return $config;
