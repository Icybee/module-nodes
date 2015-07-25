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

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

require __DIR__ . '/../vendor/autoload.php';

#
# Create the _core_ instance used for the tests.
#

$app = new Core(array_merge_recursive(get_autoconfig(), [

	'config-path' => [

		__DIR__ . DIRECTORY_SEPARATOR . 'config' => Autoconfig\Config::CONFIG_WEIGHT_MODULE

	],

	'module-path' => [

		realpath(__DIR__ . '/../')

	]

]));

$app->boot();
$app->locale = "en";

#
# Install modules
#

$errors = $app->modules->install();

if ($errors->count())
{
	foreach ($errors as $module_id => $error)
	{
		echo "$module_id: $error\n";
	}

	exit(1);
}

#
# Create a user
#

use Icybee\Modules\Users\User;
use Icybee\Modules\Sites\Site;

User::from([ 'username' => 'admin', 'email' => 'test@example.com' ])->save();
Site::from([ 'title' => 'example' ])->save();
