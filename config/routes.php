<?php

namespace Icybee\Modules\Nodes;

use ICanBoogie\Operation;

$node_routes = [];

$pathname = \ICanBoogie\REPOSITORY . 'vars/default_nodes_routes';

if (file_exists($pathname))
{
	$node_routes = require $pathname;
}

return [

	'api:nodes/online' => [

		'pattern' => '/api/:constructor/<nid:\d+>/is_online',
		'controller' => OnlineOperation::class,
		'via' => 'PUT',
		'param_translation_list' => [

			'constructor' => Operation::DESTINATION,
			'nid' => Operation::KEY

		]
	],

	'api:nodes/offline' => [

		'pattern' => '/api/:constructor/<nid:\d+>/is_online',
		'controller' => OfflineOperation::class,
		'via' => 'DELETE',
		'param_translation_list' => [

			'constructor' => Operation::DESTINATION,
			'nid' => Operation::KEY

		]
	]

] + $node_routes;
