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

use ICanBoogie\DateTime;
use ICanBoogie\HTTP\Request;
use ICanBoogie\Operation;

use Icybee\Modules\Nodes\SaveOperationTest\FakeSaveOperation;

class SaveOperationTest extends \PHPUnit_Framework_TestCase
{
	static public function setupBeforeClass()
	{
		global $core;

		$core->models['users'][1]->login();
	}

	public function test_process()
	{
		$request = Request::from([

			'is_post' => true,

			'request_params' => [

				Operation::DESTINATION => 'nodes',
				Operation::NAME => 'save',

				'title' => "My Example"

			]

		]);

		$operation = new FakeSaveOperation;
		$response = $operation($request);
		$record = $operation->record;

		$this->assertEmpty($record->uid);
		$this->assertEquals(1, $record->siteid);
		$this->assertNotEmpty($record->uuid);
		$this->assertEquals("My Example", $record->title);
		$this->assertEquals("my-example", $record->slug);
		$this->assertEquals("nodes", $record->constructor);
		$this->assertEmpty($record->is_online);
		$this->assertEmpty($record->nativeid);
		$this->assertEquals(DateTime::now()->utc, $record->created_at);
		$this->assertEquals(DateTime::now()->utc, $record->updated_at);
	}
}

namespace Icybee\Modules\Nodes\SaveOperationTest;

use ICanBoogie\HTTP\Request;

use Icybee\Modules\Nodes\SaveOperation;

class FakeSaveOperation extends SaveOperation
{
	protected function get_controls()
	{
		return [

			self::CONTROL_FORM => false

		] + parent::get_controls();
	}

	public function __invoke(Request $request)
	{
		global $core;

		$this->module = $core->modules['nodes'];

		return parent::__invoke($request);
	}
}