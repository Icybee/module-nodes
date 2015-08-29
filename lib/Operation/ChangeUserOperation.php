<?php

namespace Icybee\Modules\Nodes\Operation;

use ICanBoogie\Errors;
use ICanBoogie\HTTP\Request;
use ICanBoogie\Module;
use ICanBoogie\Operation;

use Icybee\Binding\ObjectBindings;
use Icybee\Modules\Nodes\Node;

/**
 * Changes the user of a record for another.
 *
 * @property Node $record
 */
class ChangeUserOperation extends Operation
{
	use ObjectBindings;

	/**
	 * Identifier of the new user.
	 *
	 * @var int
	 */
	private $uid;

	/**
	 * @inheritdoc
	 */
	protected function get_controls()
	{
		return [

			self::CONTROL_PERMISSION => Module::PERMISSION_ADMINISTER,
			self::CONTROL_METHOD => Request::METHOD_POST,
			self::CONTROL_RECORD => true

		] + parent::get_controls();
	}

	/**
	 * @inheritdoc
	 */
	protected function validate(Errors $errors)
	{
		$uid = $this->request['uid'];

		if (!$uid)
		{
			$errors->add('uid', "User identifier is required (uid).");
		}

		if (!$this->app->models['users']->exists($uid))
		{
			$errors->add('uid', "Invalid user identifier: %uid.", [ 'uid' => $uid ]);
		}

		$this->uid = $uid;

		return $errors;
	}

	/**
	 * @inheritdoc
	 */
	protected function process()
	{
		$record = $this->record;
		$record->uid = $this->uid;
		$record->save();

		return true;
	}
}
