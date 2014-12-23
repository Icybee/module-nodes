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

			Model::T_CONSTRUCTOR => $this->id

		];
	}

	static public function create_default_routes()
	{
		$routes = [];
		$app = \ICanBoogie\app();
		$modules = $app->modules;

		foreach ($modules->enabled_modules_descriptors as $module_id => $descriptor)
		{
			if ($module_id == 'nodes' || $module_id == 'contents' || !$modules->is_inheriting($module_id, 'nodes'))
			{
				continue;
			}

			$common = [

				'module' => $module_id,
				'controller' => 'Icybee\BlockController',
				'visibility' => 'visible'

			];

			# manage (index)

			$routes["admin:$module_id"] = [

				'pattern' => "/admin/$module_id",
				'title' => '.manage',
				'block' => 'manage',
				'index' => true

			] + $common;

			if ($module_id == 'contents' || $modules->is_inheriting($module_id, 'contents') || $module_id == 'files' || $modules->is_inheriting($module_id, 'files'))
			{
				# config'

				$routes["admin:$module_id/config"] = [

					'pattern' => "/admin/$module_id/config",
					'title' => '.config',
					'block' => 'config',
					'permission' => self::PERMISSION_ADMINISTER,

				] + $common;
			}

			# create

			$routes["admin:$module_id/new"] = [

				'pattern' => "/admin/$module_id/new",
				'title' => '.new',
				'block' => 'edit'

			] + $common;

			# edit

			$routes["admin:$module_id/edit"] = [

				'pattern' => "/admin/$module_id/<\d+>/edit",
				'controller' => 'Icybee\EditController',
				'title' => '.edit',
				'block' => 'edit',
				'visibility' => 'auto'

			] + $common;

			# delete

			$routes["admin:$module_id/delete"] = [

				'pattern' => "/admin/$module_id/<\d+>/delete",
				'controller' => 'Icybee\DeleteController',
				'title' => '.delete',
				'block' => 'delete',
				'visibility' => 'auto'

			] + $common;
		}

		new Module\CreateDefaultRoutesEvent($modules['nodes'], [ 'routes' => &$routes ]);

		$export = var_export($routes,true);

		$app->vars['default_nodes_routes'] = "<?php\n\nreturn " . $export . ';';
	}
}

namespace Icybee\Modules\Nodes\Module;

/**
 * Event class for the `Icybee\Modules\Nodes\Module::create_default_routes` event.
 */
class CreateDefaultRoutesEvent extends \ICanBoogie\Event
{
	/**
	 * Reference to the default routes.
	 *
	 * @var array[string]array
	 */
	public $routes;

	/**
	 * The event is created with the type `create_default_routes`.
	 *
	 * @param \Icybee\Modules\Nodes\Module $target
	 * @param array $payload
	 */
	public function __construct(\Icybee\Modules\Nodes\Module $target, array $payload)
	{
		parent::__construct($target, 'create_default_routes', $payload);
	}
}
