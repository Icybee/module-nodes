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

/**
 * - Renames the `created` columns as `created_as`.
 * - Renames the `modified` columns as `updated_as`.
 *
 * @module nodes
 */
class Update20131208 extends \ICanBoogie\Updater\Update
{
	public function update_column_created_at()
	{
		$this->module->model
		->assert_has_column('created')
		->rename_column('created', 'created_at');
	}

	public function update_column_updated_at()
	{
		$this->module->model
		->assert_has_column('modified')
		->create_column('updated_at');
	}
}