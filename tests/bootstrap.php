<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ICanBoogie\Events;

require __DIR__ . '/../vendor/autoload.php';

$events = new Events();

Events::patch('get', function() use($events) { return $events; });