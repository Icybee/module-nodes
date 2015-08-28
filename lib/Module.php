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
 * Nodes module.
 *
 * @property-read NodeModel $model
 */
class Module extends \Icybee\Module
{
	const OPERATION_ONLINE = 'online';
	const OPERATION_OFFLINE = 'offline';
	const OPERATION_CHANGE_USER = 'change_user';

	const PERMISSION_MODIFY_BELONGING_SITE = 'modify belonging site';

	protected function resolve_primary_model_tags($tags)
	{
		return parent::resolve_model_tags($tags, 'primary') + [

			NodeModel::CONSTRUCTOR => $this->id

		];
	}
}
