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

use Icybee\Modules\Users\User;

global $core;

require __DIR__ . '/../vendor/autoload.php';

#
# Create the _core_ instance used for the tests.
#

$core = new Core(array(

	'connections' => array
	(
		'primary' => array
		(
			'dsn' => 'sqlite::memory:'
		)
	),

	'modules paths' => array
	(
		__DIR__ . '/../',
		__DIR__ . '/../vendor/icanboogie-modules'
	)

));

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

$user = User::from(array('username' => 'admin', 'email' => 'test@example.com'));
$user->save();