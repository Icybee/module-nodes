<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes;

use ICanBoogie\Core;

class ModelTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Core|\Icybee\Binding\Core\CoreBindings|\Icybee\Modules\Sites\Binding\CoreBindings
	 */
	static private $app;

	/**
	 * @var NodeModel
	 */
	static private $model;

	static public function setupBeforeClass()
	{
		self::$app = \ICanBoogie\app();
		self::$model = self::$app->models['nodes'];
	}

	public function test_scope_similar_site()
	{
		$query = self::$model->similar_site;
		$this->assertEquals('SELECT * FROM `nodes` `node` WHERE (site_id = 0 OR site_id = ?)', (string) $query);
		$this->assertEquals([ self::$app->site_id ], $query->conditions_args);

		$query = self::$model->similar_site(123);
		$this->assertEquals([ 123 ], $query->conditions_args);
	}

	public function test_scope_similar_language()
	{
		$query = self::$model->similar_language;
		$this->assertEquals('SELECT * FROM `nodes` `node` WHERE (language = "" OR language = ?)', (string) $query);
		$this->assertEquals([ self::$app->language ], $query->conditions_args);

		$query = self::$model->similar_language('de');
		$this->assertEquals([ 'de' ], $query->conditions_args);
	}
}
