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

use ICanBoogie\Errors;
use ICanBoogie\Operation;

/**
 * @property Node $record
 */
class OnlineOperation extends Operation
{
	/**
	 * Controls for the operation: permission(maintain), record and ownership.
	 *
	 * @inheritdoc
	 */
	protected function get_controls()
	{
		return [

			self::CONTROL_PERMISSION => Module::PERMISSION_MAINTAIN,
			self::CONTROL_RECORD => true,
			self::CONTROL_OWNERSHIP => true

		] + parent::get_controls();
	}

	/**
	 * @inheritdoc
	 */
	protected function validate(Errors $errors)
	{
		return true;
	}

	/**
	 * Changes the target record is_online property to true and updates the record.
	 *
	 * @inheritdoc
	 */
	protected function process()
	{
		$record = $this->record;
		$record->is_online = true;
		$record->save();

		$this->response->message = $this->format('%title is now online', [

			'title' => \ICanBoogie\shorten($this->record->title)

		]);

		return true;
	}
}
