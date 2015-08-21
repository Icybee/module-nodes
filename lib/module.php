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

use Icybee\Modules\Views\ViewOptions;

/**
 * Nodes module.
 */
class Module extends \Icybee\Module
{
	const PERMISSION_MODIFY_BELONGING_SITE = 'modify belonging site';

	/**
	 * Defines the "view", "list" and "home" views.
	 */
	protected function lazy_get_views()
	{
		return \ICanBoogie\array_merge_recursive(parent::lazy_get_views(), [

			'view' => [

				ViewOptions::TITLE => "Record detail",
				ViewOptions::PROVIDER_CLASSNAME => ViewOptions::PROVIDER_CLASSNAME_AUTO,
				ViewOptions::RENDERS => ViewOptions::RENDERS_ONE

			],

			'list' => [

				ViewOptions::TITLE => "Records list",
				ViewOptions::PROVIDER_CLASSNAME => ViewOptions::PROVIDER_CLASSNAME_AUTO,
				ViewOptions::RENDERS => ViewOptions::RENDERS_MANY,
				ViewOptions::DEFAULT_CONDITIONS => [

					'order' => '-created_at'

				]
			],

			'home' => [

				ViewOptions::TITLE => "Records home",
				ViewOptions::PROVIDER_CLASSNAME => ViewOptions::PROVIDER_CLASSNAME_AUTO,
				ViewOptions::RENDERS => ViewOptions::RENDERS_MANY,
				ViewOptions::DEFAULT_CONDITIONS => [

					'order' => '-created_at'

				]
			]

		]);
	}

	protected function resolve_primary_model_tags($tags)
	{
		return parent::resolve_model_tags($tags, 'primary') + [

			NodeModel::CONSTRUCTOR => $this->id

		];
	}
}
