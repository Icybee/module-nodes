<?php

namespace Icybee\Modules\Nodes;

use ICanBoogie\Operation;

use Icybee\Modules\Nodes\Operation\OfflineOperation;
use Icybee\Modules\Nodes\Operation\OnlineOperation;

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

];
