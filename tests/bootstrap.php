<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ICanBoogie\Core;
use ICanBoogie\Errors;

use Icybee\Modules\Sites\Site;
use Icybee\Modules\Users\User;

#
# Create the repository for Vars
#

define('ICanBoogie\REPOSITORY', __DIR__ . DIRECTORY_SEPARATOR . 'repository' . DIRECTORY_SEPARATOR);

if (!file_exists(\ICanBoogie\REPOSITORY))
{
	mkdir(\ICanBoogie\REPOSITORY);
}

if (!file_exists(\ICanBoogie\REPOSITORY . 'vars'))
{
	mkdir(\ICanBoogie\REPOSITORY . 'vars');
}

require __DIR__ . '/../vendor/autoload.php';

#
# Create the _core_ instance used for the tests.
#

global $core;

$core = new Core(\ICanBoogie\array_merge_recursive(\ICanBoogie\get_autoconfig(), array(

	'config-path' => array
	(
		__DIR__ . DIRECTORY_SEPARATOR . 'config'
	),

	'module-path' => array
	(
		__DIR__ . '/../'
	)

)));

$core();

#
# Install modules
#

$errors = new Errors();

foreach (array_keys($core->modules->enabled_modules_descriptors) as $module_id)
{
	#
	# The index on the `constructor` column of the `nodes` module clashes with SQLite, we don't
	# care right now, so the exception is discarted.
	#

	try
	{
		$core->modules[$module_id]->install($errors);
	}
	catch (\Exception $e) {}
}

#
# Create a user
#

User::from(array('username' => 'admin', 'email' => 'test@example.com'))->save();
Site::from(array('title' => 'example'))->save();