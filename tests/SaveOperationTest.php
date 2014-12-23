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
		self::$user = \ICanBoogie\app()->models['users'][1];
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

	public function test_get_uuid()
	{
		$operation = new FakeSaveOperation;
		$uuid = $operation->uuid;

		$this->assertNotEmpty($uuid);
		$this->assertSame($uuid, $operation->uuid);
	}
}

namespace Icybee\Modules\Nodes\SaveOperationTest;

class FakeSaveOperation extends \Icybee\Modules\Nodes\SaveOperation
{
	public function __construct()
	{
		$this->module = \ICanBoogie\app()->modules['nodes'];
	}

	protected function get_controls()
	{
		return [

			self::CONTROL_FORM => false

		] + parent::get_controls();
	}
}
