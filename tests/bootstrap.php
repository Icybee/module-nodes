<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

chdir(__DIR__);

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

require __DIR__ . '/../vendor/autoload.php';

$app = boot();
$app->locale = "en";
$app->modules->install();

#
# Create user and website
#

use Icybee\Modules\Users\User;
use Icybee\Modules\Sites\Site;

User::from([

	User::USERNAME => 'admin',
	User::EMAIL => 'test@example.com',
	User::TIMEZONE => 'Europe/Paris' // this should be empty, need to update validation lib

])->save();

Site::from([

	Site::TITLE => 'example:en',
	Site::LANGUAGE => 'en',
	Site::STATUS => Site::STATUS_OK,
	Site::EMAIL => 'person@example.tld',
	Site::TIMEZONE => 'America/New_York'

])->save();

Site::from([

	Site::TITLE => 'example:fr',
	Site::LANGUAGE => 'fr',
	Site::STATUS => Site::STATUS_OK,
	Site::EMAIL => 'person@example.tld',
	Site::TIMEZONE => 'Europe/Paris'

])->save();
