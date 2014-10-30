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

class DeleteOperation extends \ICanBoogie\DeleteOperation
{
	/**
	 * Overrides the method to create a nicer log entry.
	 */
	protected function process()
	{
		$title = $this->record->title;
		$rc = parent::process();

		if ($rc)
		{
			$this->response->message = $this->format('%title has been deleted from %module.', [

				'title' => \ICanBoogie\shorten($title),
				'module' => $this->module->title

			]);
		}

		return $rc;
	}
}
