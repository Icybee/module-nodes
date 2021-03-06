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

use ICanBoogie\Updater\AssertionFailed;
use ICanBoogie\Updater\Update;

/**
 * - Renames table `system_nodes` as `nodes`.
 * - Renames `tnid` column as `nativeid`.
 *
 * @module nodes
 */
class Update20111201 extends Update
{
	public function update_table_nodes()
	{
		$db = $this->app->db;

		if (!$db->table_exists('system_nodes'))
		{
			throw new AssertionFailed('assert_table_exists', 'system_nodes');
		}

		$db("RENAME TABLE `{prefix}system_nodes` TO `{prefix}nodes`");
	}

	public function update_constructor_type()
	{
		$db = $this->app->db;
		$db("UPDATE `{prefix}nodes` SET constructor = 'nodes' WHERE constructor = 'system.nodes'");
	}

	public function update_column_nativeid()
	{
		$this->module->model
		->assert_has_column('tnid')
		->rename_column('tnid', 'nativeid');
	}
}

/**
 * - Renames the `created` columns as `created_at`.
 * - Renames the `modified` columns as `updated_at`.
 *
 * @module nodes
 */
class Update20131208 extends Update
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
		->rename_column('modified', 'updated_at');
	}
}

/**
 * Adds the `uuid` column.
 *
 * @module nodes
 */
class Update20140405 extends Update
{
	public function update_column_uuid()
	{
		$this->module->model
		->assert_not_has_column('uuid')
		->create_column('uuid');

		#
		# Update records with UUID values.
		#

		$target = $this->module->model->target;
		$tokens = $target->select('nid, NULL')->pairs;

		foreach (array_keys($tokens) as $nid)
		{
			for (;;)
			{
				$token = \ICanBoogie\generate_v4_uuid();

				if (!in_array($token, $tokens))
				{
					break;
				}
			}

			$tokens[$nid] = $token;
		}

		$update = $target->prepare("UPDATE `{self}` SET `uuid` = ? WHERE `nid` = ?");

		foreach ($tokens as $nid => $token)
		{
			$update($token, $nid);
		}

		#
		# Create index.
		#

		$this->module->model->create_unique_index('uuid');
	}
}

/**
 * - Renames column `siteid` as `site_id`.
 *
 * @module nodes
 */
class Update20150908 extends Update
{
	public function update_column_site_id()
	{
		$this->module->model
			->assert_has_column('siteid')
			->rename_column('siteid', 'site_id');
	}
}
