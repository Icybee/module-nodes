<?php

namespace Icybee\Modules\Nodes;

use ICanBoogie\ActiveRecord\Model;
use ICanBoogie\Module;

return array
(
	Module::T_CATEGORY => 'contents',
	Module::T_DESCRIPTION => 'Centralized node system base',
	Module::T_MODELS => array
	(
		'primary' => array
		(
			Model::SCHEMA => array
			(
				'fields' => array
				(
					'nid' => 'serial',
					'uid' => 'foreign',
					'siteid' => 'foreign',
					'nativeid' => 'foreign',
					'constructor' => array('varchar', 64, 'indexed' => true),
					'title' => 'varchar',
					'slug' => array('varchar', 80, 'indexed' => true),
					'language' => array('varchar', 8, 'indexed' => true),
					'created' => array('timestamp', 'default' => 'CURRENT_TIMESTAMP'),
					'modified' => 'timestamp',
					'is_online' => array('boolean', 'indexed' => true)
				)
			)
		)
	),

	Module::T_NAMESPACE => __NAMESPACE__,
	Module::T_PERMISSION => false,
	Module::T_PERMISSIONS => array
	(
		'modify belonging site'
	),

	Module::T_REQUIRED => true,
	Module::T_REQUIRES => array
	(
		'sites' => '2.0',
		'users' => '2.0'
	),

	Module::T_TITLE => 'Nodes',
	Module::T_VERSION => '2.0'
);