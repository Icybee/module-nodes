<?php

namespace Icybee\Modules\Nodes;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module;

return [

	Module::T_CATEGORY => 'contents',
	Module::T_DESCRIPTION => 'Centralized node system base',
	Module::T_MODELS => [

		'primary' => [

			Model::SCHEMA => [

				'fields' => [

					'nid' => 'serial',
					'uid' => 'foreign',
					'siteid' => 'foreign',
					'nativeid' => 'foreign',
					'uuid' => [ 'char', 36, 'unique' => true, 'charset' => 'ascii/bin' ],
					'constructor' => [ 'varchar', 64, 'indexed' => true ],
					'title' => 'varchar',
					'slug' => [ 'varchar', 80, 'indexed' => true ],
					'language' => [ 'varchar', 8, 'indexed' => true ],
					'created_at' => [ 'timestamp', 'default' => 'CURRENT_TIMESTAMP' ],
					'updated_at' => 'timestamp',
					'is_online' => [ 'boolean', 'indexed' => true ]

				]

			]

		]

	],

	Module::T_NAMESPACE => __NAMESPACE__,
	Module::T_PERMISSION => false,
	Module::T_PERMISSIONS => [

		'modify belonging site'

	],

	Module::T_REQUIRED => true,
	Module::T_REQUIRES => [

		'sites' => '2.0',
		'users' => '2.0'

	],

	Module::T_TITLE => 'Nodes',
	Module::T_VERSION => '2.0'

];