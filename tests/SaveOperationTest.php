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
use ICanBoogie\HTTP\HTTPError;
use ICanBoogie\HTTP\Request;
use ICanBoogie\Operation;

use Icybee\Modules\Nodes\SaveOperationTest\FakeSaveOperation;

class SaveOperationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \Icybee\Modules\Users\User
	 */
	static private $user;

	static public function setupBeforeClass()
	{
		global $core;

		self::$user = $core->models['users'][1];
	}

	public function test_process()
	{
		self::$user->login();

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

		self::$user->logout();

		$this->assertNotEmpty($record->uid);
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

	public function test_failure_user_authentication()
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

		try
		{
			$response = $operation($request);

			$this->fail('Expected HTTPError.');
		}
		catch (HTTPError $e)
		{
			$this->assertEquals(401, $e->getCode());
		}
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
