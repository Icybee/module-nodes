<?php

namespace Icybee\Modules\Nodes;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module\Descriptor;

return [

	Descriptor::ID => 'nodes',
	Descriptor::CATEGORY => 'contents',
	Descriptor::DESCRIPTION => "Centralized node system base",
	Descriptor::MODELS => [

		'primary' => [

			Model::SCHEMA => [

				'nid' => 'serial',
				'uid' => 'foreign',
				'site_id' => 'foreign',
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
	],

	Descriptor::NS => __NAMESPACE__,
	Descriptor::PERMISSION => false,
	Descriptor::PERMISSIONS => [

		'modify belonging site'

	],

	Descriptor::REQUIRED => true,
	Descriptor::REQUIRES => [ 'sites', 'users', 'registry' ],
	Descriptor::TITLE => "Nodes"

];
