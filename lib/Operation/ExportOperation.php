<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Nodes\Operation;

use ICanBoogie\Errors;
use ICanBoogie\Module;
use ICanBoogie\Operation;

use Icybee\Binding\PrototypedBindings;

class ExportOperation extends Operation
{
	use PrototypedBindings;

	protected function get_controls()
	{
		return [

			self::CONTROL_PERMISSION => Module::PERMISSION_ADMINISTER

		] + parent::get_controls();
	}

	protected function validate(Errors $errors)
	{
		return true;
	}

	protected function process()
	{
		$records = $this->module->model
			->filter_by_siteid($this->app->site_id)
			->own
			->all(\PDO::FETCH_OBJ);

		$by_id = [];

		foreach ($records as $record)
		{
			$by_id[$record->nid] = $record;

			unset($record->nid);
		}

		return $by_id;
	}
}
